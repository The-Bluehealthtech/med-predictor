#!/usr/bin/env python3
"""Private stdin/stdout DICOM adapter. Never fetches URLs or interprets age."""
import base64, io, json, sys
from datetime import datetime
import numpy as np
import pydicom
from PIL import Image
from pydicom.dataset import Dataset, FileDataset, FileMetaDataset
from pydicom.sequence import Sequence
from pydicom.uid import ExplicitVRLittleEndian, EnhancedSRStorage, SecondaryCaptureImageStorage
from pydicom.pixels import pixel_array, apply_color_lut


def code(value, meaning, scheme='DCM'):
    d=Dataset(); d.CodeValue=value; d.CodingSchemeDesignator=scheme; d.CodeMeaning=meaning
    return d


def file_dataset(sop_class, uid):
    meta=FileMetaDataset(); meta.MediaStorageSOPClassUID=sop_class; meta.MediaStorageSOPInstanceUID=uid
    meta.TransferSyntaxUID=ExplicitVRLittleEndian; meta.ImplementationClassUID='2.25.31886729955717587570641953829904489343'
    ds=FileDataset(None,{},file_meta=meta,preamble=b'\0'*128)
    ds.SOPClassUID=sop_class; ds.SOPInstanceUID=uid; ds.SpecificCharacterSet='ISO_IR 192'
    return ds


def encode(ds):
    b=io.BytesIO(); ds.save_as(b,enforce_file_format=True); return base64.b64encode(b.getvalue()).decode()


def inspect(p):
    raw=base64.b64decode(p['content'],validate=True)
    if p['mime'] in ('image/jpeg','image/png'):
        img=Image.open(io.BytesIO(raw)); img.verify()
        img=Image.open(io.BytesIO(raw))
        if img.width*img.height*3>20*1024*1024-65536: raise ValueError('converted image exceeds 20 MiB')
        img=img.convert('RGB')
        ds=file_dataset(SecondaryCaptureImageStorage,p['sop_uid'])
        ds.PatientID=p['patient']['id']; ds.PatientName=p['patient']['name']; ds.PatientBirthDate=p['patient'].get('birth_date',''); ds.PatientSex=p['patient'].get('sex','')
        ds.PatientOrientation=''; ds.Manufacturer='FIT'; ds.ImageComments='Imported raster image; no physical calibration inferred'
        ds.StudyInstanceUID=p['study_uid']; ds.SeriesInstanceUID=p['series_uid']; ds.Modality='OT'
        ds.StudyDate=p['exam_date']; ds.StudyTime=''; ds.SeriesNumber=1; ds.InstanceNumber=1; ds.AccessionNumber=''; ds.ReferringPhysicianName=''; ds.StudyID=''
        ds.ConversionType='WSD'; ds.ImageType=['DERIVED','SECONDARY']; ds.ContentDate=p['exam_date']; ds.ContentTime=''
        ds.Rows=img.height; ds.Columns=img.width; ds.SamplesPerPixel=3; ds.PhotometricInterpretation='RGB'; ds.PlanarConfiguration=0
        ds.BitsAllocated=8; ds.BitsStored=8; ds.HighBit=7; ds.PixelRepresentation=0; ds.PixelData=img.tobytes()
        content=encode(ds)
    else:
        ds=pydicom.dcmread(io.BytesIO(raw),force=False)
        if 'PixelData' not in ds and 'FloatPixelData' not in ds: raise ValueError('not a pixel image')
        content=p['content']
    content_bytes=len(content)*3//4-len(content)+len(content.rstrip('='))
    if content_bytes>20*1024*1024: raise ValueError('DICOM object exceeds 20 MiB')
    if int(ds.Rows)*int(ds.Columns)>25000000 or int(getattr(ds,'NumberOfFrames',1))>10000: raise ValueError('image dimensions')
    if int(getattr(ds,'NumberOfFrames',1))>1 and str(ds.SOPClassUID) in ('1.2.840.10008.5.1.4.1.1.4','1.2.840.10008.5.1.4.1.1.2','1.2.840.10008.5.1.4.1.1.7','1.2.840.10008.5.1.4.1.1.1','1.2.840.10008.5.1.4.1.1.1.1','1.2.840.10008.5.1.4.1.1.1.2'):
        raise ValueError('invalid multiframe SOP class')
    for name in ('StudyInstanceUID','SeriesInstanceUID','SOPInstanceUID','SOPClassUID'):
        if not getattr(ds,name,None) or not pydicom.uid.UID(str(getattr(ds,name))).is_valid: raise ValueError('missing UID')
    def frame_spacing(index):
        spacing=[float(v) for v in getattr(ds,'PixelSpacing',[])]
        for groups in (getattr(ds,'SharedFunctionalGroupsSequence',[]),getattr(ds,'PerFrameFunctionalGroupsSequence',[])[index:index+1]):
            if groups and hasattr(groups[0],'PixelMeasuresSequence'):
                spacing=[float(v) for v in getattr(groups[0].PixelMeasuresSequence[0],'PixelSpacing',[])]
        return spacing
    wc=getattr(ds,'WindowCenter',None); ww=getattr(ds,'WindowWidth',None)
    def first(v):
        if v is None: return None
        return float(v[0] if hasattr(v,'__iter__') and not isinstance(v,str) else v)
    md={'content_bytes':content_bytes,'study_uid':str(ds.StudyInstanceUID),'series_uid':str(ds.SeriesInstanceUID),'sop_uid':str(ds.SOPInstanceUID),'sop_class_uid':str(ds.SOPClassUID),
        'patient':{'id':str(getattr(ds,'PatientID','')),'issuer':str(getattr(ds,'IssuerOfPatientID','')),'name':str(getattr(ds,'PatientName','')),'birth_date':str(getattr(ds,'PatientBirthDate','')),'sex':str(getattr(ds,'PatientSex',''))},
        'modality':str(ds.Modality),'study_date':str(getattr(ds,'StudyDate','')),'study_time':str(getattr(ds,'StudyTime','')),'accession':str(getattr(ds,'AccessionNumber','')),
        'series_description':str(getattr(ds,'SeriesDescription','Série')),'series_number':int(getattr(ds,'SeriesNumber',0) or 0),'instance_number':int(getattr(ds,'InstanceNumber',0) or 0),
        'rows':int(ds.Rows),'columns':int(ds.Columns),'frames':int(getattr(ds,'NumberOfFrames',1)),
        'pixel_spacing':frame_spacing(0),'frame_pixel_spacing':[frame_spacing(i) for i in range(int(getattr(ds,'NumberOfFrames',1)))], 'window_center':first(wc),'window_width':first(ww),
        'transfer_syntax':str(ds.file_meta.TransferSyntaxUID),'synthetic_source':p['mime']!='application/dicom'}
    return {'metadata':md,'content':content}


def render(p):
    ds=pydicom.dcmread(io.BytesIO(base64.b64decode(p['content'],validate=True)))
    frame=int(p.get('frame',0)); frames=int(getattr(ds,'NumberOfFrames',1))
    if not 0<=frame<frames: raise ValueError('frame')
    arr=pixel_array(ds,index=frame)
    if getattr(ds,'PhotometricInterpretation','')=='PALETTE COLOR':
        arr=apply_color_lut(arr,ds)
        if arr.dtype==np.uint16: arr=(arr/257).astype(np.uint8)
    elif int(getattr(ds,'SamplesPerPixel',1))==1:
        slope=float(getattr(ds,'RescaleSlope',1)); intercept=float(getattr(ds,'RescaleIntercept',0))
        for groups in (getattr(ds,'SharedFunctionalGroupsSequence',[]),getattr(ds,'PerFrameFunctionalGroupsSequence',[])[frame:frame+1]):
            if groups and hasattr(groups[0],'PixelValueTransformationSequence'):
                transform=groups[0].PixelValueTransformationSequence[0];slope=float(transform.RescaleSlope);intercept=float(transform.RescaleIntercept)
        arr=arr.astype(np.float64)*slope+intercept
        center=p.get('center'); width=p.get('width')
        if center is not None and width is not None:
            lo=float(center)-float(width)/2; hi=float(center)+float(width)/2
        else:
            lo=float(np.min(arr)); hi=float(np.max(arr))
        arr=np.clip((arr-lo)*255/max(hi-lo,1e-8),0,255).astype(np.uint8)
        if getattr(ds,'PhotometricInterpretation','')=='MONOCHROME1': arr=255-arr
    else:
        if arr.dtype!=np.uint8: arr=np.clip(arr,0,255).astype(np.uint8)
    image=Image.fromarray(arr); image.thumbnail((2048,2048))
    buf=io.BytesIO(); image.save(buf,format='PNG')
    return {'content':base64.b64encode(buf.getvalue()).decode()}


def describe(p):
    # Caractéristiques techniques pour la visionneuse ; aucune donnée d'identité du patient.
    ds=pydicom.dcmread(io.BytesIO(base64.b64decode(p['content'],validate=True)))  # pixels lus, non décodés
    def first(v):
        if v is None: return None
        try: return float(v[0]) if hasattr(v,'__len__') and not isinstance(v,(str,bytes)) else float(v)
        except Exception: return None
    has_pixels='PixelData' in ds or 'FloatPixelData' in ds or 'DoubleFloatPixelData' in ds
    return {'pixels':has_pixels,'frames':int(getattr(ds,'NumberOfFrames',1) or 1),'rows':int(getattr(ds,'Rows',0) or 0),'columns':int(getattr(ds,'Columns',0) or 0),
        'modality':str(getattr(ds,'Modality','')),'photometric':str(getattr(ds,'PhotometricInterpretation','')),'samples':int(getattr(ds,'SamplesPerPixel',1) or 1),
        'window_center':first(getattr(ds,'WindowCenter',None)),'window_width':first(getattr(ds,'WindowWidth',None)),
        'transfer_syntax':str(ds.file_meta.TransferSyntaxUID.name) if 'TransferSyntaxUID' in ds.file_meta else '',
        'sop_class':str(getattr(ds,'SOPClassUID',''))}


def raster(p):
    # Images TIFF / BMP (non affichées par tous les navigateurs) converties en PNG, première page.
    img=Image.open(io.BytesIO(base64.b64decode(p['content'],validate=True)))
    if img.width*img.height>50000000: raise ValueError('image dimensions')
    img.seek(0)
    if img.mode not in ('RGB','RGBA','L','LA'): img=img.convert('RGB')
    img.thumbnail((2048,2048))
    buf=io.BytesIO(); img.save(buf,format='PNG')
    return {'content':base64.b64encode(buf.getvalue()).decode()}


def item(kind, concept, relationship='CONTAINS'):
    d=Dataset(); d.ValueType=kind; d.RelationshipType=relationship; d.ConceptNameCodeSequence=Sequence([concept]); return d


def sr(p):
    ds=file_dataset(EnhancedSRStorage,p['sop_uid']); patient=p['patient']; now=datetime.fromisoformat(p['validated_at'])
    ds.PatientID=patient['id']; ds.PatientName=patient['name']; ds.PatientBirthDate=patient.get('birth_date',''); ds.PatientSex=patient.get('sex','')
    if patient.get('issuer'): ds.IssuerOfPatientID=patient['issuer']
    ds.StudyInstanceUID=p['study_uid']; ds.SeriesInstanceUID=p['series_uid']; ds.StudyDate=p['study_date']; ds.StudyTime=p.get('study_time','')
    ds.AccessionNumber=p.get('accession',''); ds.ReferringPhysicianName=''; ds.StudyID=''
    ds.SeriesNumber=9000+p['version']; ds.InstanceNumber=p['version']; ds.Modality='SR'; ds.Manufacturer='FIT'
    ds.ContentDate=now.strftime('%Y%m%d'); ds.ContentTime=now.strftime('%H%M%S'); ds.InstanceCreationDate=ds.ContentDate; ds.InstanceCreationTime=ds.ContentTime
    ds.TimezoneOffsetFromUTC=now.strftime('%z') or '+0000'
    ds.CompletionFlag='COMPLETE'; ds.VerificationFlag='VERIFIED'; ds.PreliminaryFlag='FINAL'
    observer=Dataset(); observer.VerifyingObserverName=p['validator']; observer.VerifyingOrganization=p['organization']; observer.VerificationDateTime=now.strftime('%Y%m%d%H%M%S%z'); observer.VerifyingObserverIdentificationCodeSequence=Sequence([])
    ds.VerifyingObserverSequence=Sequence([observer]); ds.PerformedProcedureCodeSequence=Sequence([]); ds.ReferencedPerformedProcedureStepSequence=Sequence([])
    ds.ValueType='CONTAINER'; ds.ContinuityOfContent='SEPARATE'; ds.ConceptNameCodeSequence=Sequence([code('18748-4','Diagnostic Imaging Report','LN')])
    # Generic Enhanced SR: no unverified claim to a restricted DCMR template.
    contents=[]
    for key,label in [('indication','Indication'),('technique','Technique'),('quality','Qualité'),('findings','Observations'),('conclusion','Conclusion'),('age_summary','Revue U-17')]:
        if p.get(key):
            d=item('TEXT',code(key,label,'99FIT')); d.TextValue=str(p[key]); contents.append(d)
    creator=item('PNAME',code('121008','Person Observer Name'),'HAS OBS CONTEXT'); creator.PersonName=p['validator']; contents.insert(0,creator)
    for ref in p['references']:
        d=item('IMAGE',code('121112','Source of Measurement')); r=Dataset(); r.ReferencedSOPClassUID=ref['sop_class_uid']; r.ReferencedSOPInstanceUID=ref['sop_uid'];
        if ref.get('frame') is not None and int(ref.get('frames',1))>1: r.ReferencedFrameNumber=int(ref['frame'])+1
        d.ReferencedSOPSequence=Sequence([r]); contents.append(d)
        if ref.get('length_mm') is not None:
            m=item('NUM',code('length','Distance','99FIT')); v=Dataset(); v.NumericValue=str(round(float(ref['length_mm']),4)); v.MeasurementUnitsCodeSequence=Sequence([code('mm','millimeter','UCUM')]); m.MeasuredValueSequence=Sequence([v])
            roi=item('SCOORD',code('111030','Image Region'),'INFERRED FROM'); roi.GraphicType='POLYLINE'; roi.GraphicData=ref['points']; image=item('IMAGE',code('121112','Source of Measurement'),'SELECTED FROM'); image.ReferencedSOPSequence=Sequence([r]); roi.ContentSequence=Sequence([image]); m.ContentSequence=Sequence([roi]); contents.append(m)
    age=p.get('age_review') or {}
    if age.get('grade') and age.get('population')=='male':
        grade=int(age['grade']); roman=['I','II','III','IV','V','VI'][grade-1]
        d=item('CODE',code('radiusFusionGrade','Distal radius fusion grade','99FIT'))
        d.ConceptCodeSequence=Sequence([code('grade'+str(grade),'Grade '+roman,'99FIT')]); contents.append(d)
    for ref in p['references']:
        if ref.get('points') and ref.get('length_mm') is None:
            roi=item('SCOORD',code('111030','Image Region'));roi.GraphicType='POLYLINE';roi.GraphicData=ref['points']
            image=item('IMAGE',code('121112','Source of Measurement'),'SELECTED FROM');r=Dataset();r.ReferencedSOPClassUID=ref['sop_class_uid'];r.ReferencedSOPInstanceUID=ref['sop_uid']
            if int(ref.get('frames',1))>1: r.ReferencedFrameNumber=int(ref['frame'])+1
            image.ReferencedSOPSequence=Sequence([r]);roi.ContentSequence=Sequence([image]);contents.append(roi)
    ds.ContentSequence=Sequence(contents)
    scheme=Dataset(); scheme.CodingSchemeDesignator='99FIT'; scheme.CodingSchemeName='FIT imaging observations'; scheme.CodingSchemeResponsibleOrganization='FIT'; ds.CodingSchemeIdentificationSequence=Sequence([scheme])
    evidence=Dataset(); evidence.StudyInstanceUID=p['study_uid']; groups={}
    for ref in p['references']:
        groups.setdefault(ref['series_uid'],[]).append(ref)
    series=[]
    for uid,refs in groups.items():
        s=Dataset(); s.SeriesInstanceUID=uid; seen=set(); sop=[]
        for ref in refs:
            if ref['sop_uid'] in seen: continue
            seen.add(ref['sop_uid']); r=Dataset(); r.ReferencedSOPClassUID=ref['sop_class_uid']; r.ReferencedSOPInstanceUID=ref['sop_uid']; sop.append(r)
        s.ReferencedSOPSequence=Sequence(sop); series.append(s)
    evidence.ReferencedSeriesSequence=Sequence(series); ds.CurrentRequestedProcedureEvidenceSequence=Sequence([evidence])
    if p.get('predecessor'):
        prev=Dataset(); prev.StudyInstanceUID=p['study_uid']; s=Dataset(); s.SeriesInstanceUID=p['predecessor']['series_uid']; r=Dataset(); r.ReferencedSOPClassUID=EnhancedSRStorage; r.ReferencedSOPInstanceUID=p['predecessor']['sop_uid']; s.ReferencedSOPSequence=Sequence([r]); prev.ReferencedSeriesSequence=Sequence([s]); ds.PredecessorDocumentsSequence=Sequence([prev])
    return {'content':encode(ds)}


if __name__=='__main__':
    try:
        payload=json.load(sys.stdin); result={'inspect':inspect,'render':render,'sr':sr,'describe':describe,'raster':raster}[sys.argv[1]](payload); json.dump(result,sys.stdout,ensure_ascii=False)
    except Exception:
        # Patient data must not reach application logs.
        sys.stderr.write('DICOM operation failed\n'); sys.exit(1)
