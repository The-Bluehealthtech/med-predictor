"""Synthetic fixtures only: DICOM parsing, frames, SR evidence and version links."""
import base64, importlib.util, io, json, unittest
from pathlib import Path
import numpy as np
import pydicom
from pydicom.uid import EnhancedMRImageStorage as MRImageStorage
ROOT=Path(__file__).resolve().parents[2]
spec=importlib.util.spec_from_file_location('fit_dicom',ROOT/'bin/medical_imaging_dicom.py'); worker=importlib.util.module_from_spec(spec);spec.loader.exec_module(worker)
class DicomTest(unittest.TestCase):
 def setUp(self):
  self.ds=worker.file_dataset(MRImageStorage,'2.25.101');d=self.ds
  d.StudyInstanceUID='2.25.102';d.SeriesInstanceUID='2.25.103';d.Modality='MR';d.PatientID='TEST-1';d.PatientName='Fixture^Player';d.PatientBirthDate='20100201';d.PatientSex='M';d.StudyDate='20261001';d.Rows=4;d.Columns=5;d.SamplesPerPixel=1;d.PhotometricInterpretation='MONOCHROME2';d.BitsAllocated=16;d.BitsStored=16;d.HighBit=15;d.PixelRepresentation=0;d.NumberOfFrames=2;d.PixelSpacing=[.5,.5];d.PixelData=np.arange(40,dtype=np.uint16).tobytes();self.content=worker.encode(d)
 def test_parse_preserves_source_identity_and_uids(self):
  r=worker.inspect({'mime':'application/dicom','content':self.content});self.assertEqual(r['metadata']['patient']['id'],'TEST-1');self.assertEqual(r['metadata']['sop_uid'],'2.25.101');self.assertEqual(r['metadata']['frames'],2);self.assertEqual(r['content'],self.content)
 def test_multiframe_windowed_render_and_invalid_frame(self):
  r=worker.render({'content':self.content,'frame':1,'center':25,'width':20});self.assertTrue(base64.b64decode(r['content']).startswith(b'\x89PNG'))
  with self.assertRaises(ValueError):worker.render({'content':self.content,'frame':2})
 def test_non_dicom_rejected(self):
  with self.assertRaises(Exception):worker.inspect({'mime':'application/dicom','content':base64.b64encode(b'not a DICOM').decode()})
 def payload(self):
  return {'sop_uid':'2.25.201','study_uid':'2.25.102','series_uid':'2.25.202','study_date':'20261001','patient':{'id':'TEST-1','name':'Fixture^Player','birth_date':'20100201','sex':'M'},'validated_at':'2026-10-02T00:00:00+00:00','validator':'Fixture^Doctor','organization':'Fixture Centre','version':2,'findings':'Fixture finding','conclusion':'Human conclusion','age_summary':'Grade VI; no chronological age inferred','references':[{'series_uid':'2.25.103','sop_uid':'2.25.101','sop_class_uid':str(MRImageStorage),'frame':1,'frames':2,'length_mm':5,'points':[1,1,5,4]}],'predecessor':{'series_uid':'2.25.200','sop_uid':'2.25.199'}}
 def test_sr_roundtrip_preserves_evidence_units_authorship_and_predecessor(self):
  result=worker.sr(self.payload());d=pydicom.dcmread(io.BytesIO(base64.b64decode(result['content'])))
  self.assertEqual(d.Modality,'SR');self.assertEqual(d.CompletionFlag,'COMPLETE');self.assertEqual(d.VerificationFlag,'VERIFIED');self.assertEqual(str(d.VerifyingObserverSequence[0].VerifyingObserverName),'Fixture^Doctor')
  evidence=d.CurrentRequestedProcedureEvidenceSequence[0];self.assertEqual(evidence.StudyInstanceUID,'2.25.102');self.assertEqual(evidence.ReferencedSeriesSequence[0].ReferencedSOPSequence[0].ReferencedSOPInstanceUID,'2.25.101')
  m=next(i for i in d.ContentSequence if i.ValueType=='NUM');self.assertEqual(float(m.MeasuredValueSequence[0].NumericValue),5);self.assertEqual(m.MeasuredValueSequence[0].MeasurementUnitsCodeSequence[0].CodeValue,'mm')
  self.assertEqual(m.ContentSequence[0].ContentSequence[0].ReferencedSOPSequence[0].ReferencedFrameNumber,2)
  self.assertEqual(d.PredecessorDocumentsSequence[0].ReferencedSeriesSequence[0].ReferencedSOPSequence[0].ReferencedSOPInstanceUID,'2.25.199')
  self.assertNotIn('ContentTemplateSequence',d)
  p=self.payload();p['references'][0]['frames']=1;p['references'][0]['frame']=0
  single=pydicom.dcmread(io.BytesIO(base64.b64decode(worker.sr(p)['content'])))
  direct=next(i for i in single.ContentSequence if i.ValueType=='IMAGE');self.assertNotIn('ReferencedFrameNumber',direct.ReferencedSOPSequence[0])
  (ROOT/'storage/app').mkdir(parents=True,exist_ok=True);(ROOT/'storage/app/imaging-test-sr.dcm').write_bytes(base64.b64decode(result['content']))
 def test_png_is_explicit_secondary_capture(self):
  b=io.BytesIO();worker.Image.new('RGB',(4,4),(50,100,200)).save(b,format='PNG')
  p={'mime':'image/png','content':base64.b64encode(b.getvalue()).decode(),'sop_uid':'2.25.301','study_uid':'2.25.302','series_uid':'2.25.303','exam_date':'20261001','patient':{'id':'TEST-2','name':'Fixture^Photo'}}
  r=worker.inspect(p);self.assertTrue(r['metadata']['synthetic_source']);self.assertEqual(r['metadata']['pixel_spacing'],[]);d=pydicom.dcmread(io.BytesIO(base64.b64decode(r['content'])));self.assertEqual(d.ConversionType,'WSD')
if __name__=='__main__':unittest.main()
