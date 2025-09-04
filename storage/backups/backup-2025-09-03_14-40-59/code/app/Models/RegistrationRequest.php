<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'association',
        'profile_type',
        'organization',
        'first_name',
        'last_name',
        'email',
        'phone',
        'reason',
        'status',
        'admin_notes',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the admin who reviewed this request.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the full name of the requester.
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the association display name.
     */
    public function getAssociationNameAttribute()
    {
        $associationNames = [
            'england' => 'The Football Association (FA)',
            'spain' => 'Real Federación Española de Fútbol (RFEF)',
            'germany' => 'Deutscher Fußball-Bund (DFB)',
            'france' => 'Fédération Française de Football (FFF)',
            'italy' => 'Federazione Italiana Giuoco Calcio (FIGC)',
            'netherlands' => 'Koninklijke Nederlandse Voetbalbond (KNVB)',
            'portugal' => 'Federação Portuguesa de Futebol (FPF)',
            'belgium' => 'Union Royale Belge des Sociétés de Football (URBSFA)',
            'switzerland' => 'Schweizerischer Fussballverband (SFV)',
            'austria' => 'Österreichischer Fußball-Bund (ÖFB)',
            'sweden' => 'Svenska Fotbollförbundet (SvFF)',
            'norway' => 'Norges Fotballforbund (NFF)',
            'denmark' => 'Dansk Boldspil-Union (DBU)',
            'finland' => 'Suomen Palloliitto (SPL)',
            'poland' => 'Polski Związek Piłki Nożnej (PZPN)',
            'czech' => 'Fotbalová asociace České republiky (FAČR)',
            'hungary' => 'Magyar Labdarúgó Szövetség (MLSZ)',
            'romania' => 'Federația Română de Fotbal (FRF)',
            'bulgaria' => 'Български футболен съюз (БФС)',
            'croatia' => 'Hrvatski nogometni savez (HNS)',
            'serbia' => 'Фудбалски савез Србије (ФСС)',
            'slovenia' => 'Nogometna zveza Slovenije (NZS)',
            'slovakia' => 'Slovenský futbalový zväz (SFZ)',
            'ukraine' => 'Українська асоціація футболу (УАФ)',
            'russia' => 'Российский футбольный союз (РФС)',
            'turkey' => 'Türkiye Futbol Federasyonu (TFF)',
            'greece' => 'Ελληνική Ποδοσφαιρική Ομοσπονδία (ΕΠΟ)',
            'cyprus' => 'Κυπριακή Ομοσπονδία Ποδοσφαίρου (ΚΟΠ)',
            'malta' => 'Malta Football Association (MFA)',
            'iceland' => 'Knattspyrnusamband Íslands (KSÍ)',
            'ireland' => 'Football Association of Ireland (FAI)',
            'northern_ireland' => 'Irish Football Association (IFA)',
            'scotland' => 'Scottish Football Association (SFA)',
            'wales' => 'Football Association of Wales (FAW)'
        ];

        return $associationNames[$this->association] ?? $this->association;
    }

    /**
     * Get the profile type display name.
     */
    public function getProfileTypeNameAttribute()
    {
        $profileTypeNames = [
            'club' => 'Club Management',
            'association' => 'Association',
            'player' => 'Player',
            'referee' => 'Referee',
            'medical_staff' => 'Medical Staff'
        ];

        return $profileTypeNames[$this->profile_type] ?? $this->profile_type;
    }

    /**
     * Scope to filter by association.
     */
    public function scopeForAssociation($query, $association)
    {
        return $query->where('association', $association);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
