<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'association' => ['nullable', 'string'],
            'access_type' => ['nullable', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Get the authenticated user
        $user = Auth::user();
        
        // Store access type in session for player access validation
        $accessType = $this->input('access_type');
        if ($accessType) {
            session(['login_access_type' => $accessType]);
        }

        // Set redirect URL based on user role and access type
        if ($accessType === 'player' && $user->role === 'player') {
            session(['intended_url' => route('player-dashboard.index')]);
        }

        // Temporarily skip association validation for testing
        // $this->validateAssociationAccess($user);

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Validate that the user can access the requested association and role.
     */
    private function validateAssociationAccess(User $user): void
    {
        $requestedAssociation = $this->input('association');
        $requestedAccessType = $this->input('access_type');

        // If no specific association/access type requested, allow access
        if (!$requestedAssociation && !$requestedAccessType) {
            return;
        }

        // Check if user belongs to the requested association
        if ($requestedAssociation) {
            // Get the user's association code
            $userAssociationCode = $this->getUserAssociationCode($user);
            
            if ($userAssociationCode !== $requestedAssociation) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Vous n\'êtes pas autorisé à accéder à cette association.',
                ]);
            }
        }

        // Check if user has the appropriate role for the requested access type
        if ($requestedAccessType) {
            $allowedRoles = $this->getAllowedRolesForAccessType($requestedAccessType);
            
            if (!in_array($user->role, $allowedRoles)) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Vous n\'avez pas les permissions nécessaires pour ce type d\'accès.',
                ]);
            }
        }
    }

    /**
     * Get allowed roles for each access type.
     */
    private function getAllowedRolesForAccessType(string $accessType): array
    {
        $roleMap = [
            'club' => ['club_admin', 'club_manager', 'club_staff', 'club_medical'],
            'association' => ['association_admin', 'association_staff', 'association_registrar', 'association_medical', 'admin'],
            'player' => ['player'],
            'referee' => ['referee'],
            'medical_staff' => ['medical_staff', 'medical_admin', 'club_medical', 'association_medical'],
        ];

        return $roleMap[$accessType] ?? [];
    }

    /**
     * Get the user's association code.
     */
    private function getUserAssociationCode(User $user): ?string
    {
        // Check if user has a direct association relationship
        if ($user->association) {
            return $this->mapAssociationNameToCode($user->association->name);
        }

        // Check if user has a club with an association
        if ($user->club && $user->club->association) {
            return $this->mapAssociationNameToCode($user->club->association->name);
        }

        // Check legacy entity relationships
        if ($user->entity_type === 'association' && $user->entity_id) {
            $association = \App\Models\Association::find($user->entity_id);
            return $association ? $this->mapAssociationNameToCode($association->name) : null;
        }

        if ($user->entity_type === 'club' && $user->entity_id) {
            $club = \App\Models\Club::find($user->entity_id);
            if ($club && $club->association) {
                return $this->mapAssociationNameToCode($club->association->name);
            }
        }

        return null;
    }

    /**
     * Map association names to frontend codes.
     */
    private function mapAssociationNameToCode(string $associationName): string
    {
        $nameToCodeMap = [
            // UEFA - Europe
            'The Football Association' => 'england',
            'Real Federación Española de Fútbol' => 'spain',
            'Deutscher Fußball-Bund' => 'germany',
            'Fédération Française de Football' => 'france',
            'Federazione Italiana Giuoco Calcio' => 'italy',
            'Koninklijke Nederlandse Voetbalbond' => 'netherlands',
            'Federação Portuguesa de Futebol' => 'portugal',
            'Fédération Royale Belge de Football' => 'belgium',
            'Schweizerischer Fussballverband' => 'switzerland',
            'Österreichischer Fußball-Bund' => 'austria',
            'Svenska Fotbollförbundet' => 'sweden',
            'Norges Fotballforbund' => 'norway',
            'Dansk Boldspil-Union' => 'denmark',
            'Suomen Palloliitto' => 'finland',
            'Polski Związek Piłki Nożnej' => 'poland',
            'Fotbalová asociace České republiky' => 'czech',
            'Magyar Labdarúgó Szövetség' => 'hungary',
            'Federația Română de Fotbal' => 'romania',
            'Български футболен съюз' => 'bulgaria',
            'Hrvatski nogometni savez' => 'croatia',
            'Фудбалски савез Србије' => 'serbia',
            'Nogometna zveza Slovenije' => 'slovenia',
            'Slovenský futbalový zväz' => 'slovakia',
            'Українська асоціація футболу' => 'ukraine',
            'Российский футбольный союз' => 'russia',
            'Türkiye Futbol Federasyonu' => 'turkey',
            'Ελληνική Ποδοσφαιρική Ομοσπονδία' => 'greece',
            'Κυπριακή Ομοσπονδία Ποδοσφαίρου' => 'cyprus',
            'Malta Football Association' => 'malta',
            'Knattspyrnusamband Íslands' => 'iceland',
            'Football Association of Ireland' => 'ireland',
            'Irish Football Association' => 'northern_ireland',
            'Scottish Football Association' => 'scotland',
            'Football Association of Wales' => 'wales',

            // CONMEBOL - South America
            'Confederação Brasileira de Futebol' => 'brazil',
            'Asociación del Fútbol Argentino' => 'argentina',
            'Asociación Uruguaya de Fútbol' => 'uruguay',
            'Federación de Fútbol de Chile' => 'chile',
            'Federación Colombiana de Fútbol' => 'colombia',
            'Federación Peruana de Fútbol' => 'peru',
            'Federación Ecuatoriana de Fútbol' => 'ecuador',
            'Asociación Paraguaya de Fútbol' => 'paraguay',
            'Federación Boliviana de Fútbol' => 'bolivia',
            'Federación Venezolana de Fútbol' => 'venezuela',

            // CONCACAF - North, Central America and Caribbean
            'United States Soccer Federation' => 'usa',
            'Federación Mexicana de Fútbol' => 'mexico',
            'Canada Soccer' => 'canada',
            'Federación Costarricense de Fútbol' => 'costa_rica',
            'Jamaica Football Federation' => 'jamaica',
            'Federación Nacional Autónoma de Fútbol de Honduras' => 'honduras',
            'Federación Panameña de Fútbol' => 'panama',
            'Trinidad and Tobago Football Association' => 'trinidad_tobago',
            'Fédération Haïtienne de Football' => 'haiti',
            'Federación Salvadoreña de Fútbol' => 'el_salvador',

            // CAF - Africa
            'Nigeria Football Federation' => 'nigeria',
            'Ghana Football Association' => 'ghana',
            'Fédération Camerounaise de Football' => 'cameroon',
            'Fédération Sénégalaise de Football' => 'senegal',
            'Fédération Royale Marocaine de Football' => 'morocco',
            'Fédération Tunisienne de Football' => 'tunisia',
            'Fédération Algérienne de Football' => 'algeria',
            'Egyptian Football Association' => 'egypt',
            'South African Football Association' => 'south_africa',
            'Fédération Ivoirienne de Football' => 'ivory_coast',
            'Football Kenya Federation' => 'kenya',
            'Federation of Uganda Football Associations' => 'uganda',
            'Tanzania Football Federation' => 'tanzania',
            'Football Association of Zambia' => 'zambia',
            'Zimbabwe Football Association' => 'zimbabwe',

            // AFC - Asia
            'Japan Football Association' => 'japan',
            'Korea Football Association' => 'south_korea',
            'Chinese Football Association' => 'china',
            'Football Australia' => 'australia',
            'Football Federation Islamic Republic of Iran' => 'iran',
            'Saudi Arabian Football Federation' => 'saudi_arabia',
            'Qatar Football Association' => 'qatar',
            'UAE Football Association' => 'uae',
            'All India Football Federation' => 'india',
            'Football Association of Thailand' => 'thailand',
            'Vietnam Football Federation' => 'vietnam',
            'Football Association of Malaysia' => 'malaysia',
            'Football Association of Singapore' => 'singapore',
            'Football Association of Indonesia' => 'indonesia',
            'Philippine Football Federation' => 'philippines',

            // OFC - Oceania
            'New Zealand Football' => 'new_zealand',
            'Fiji Football Association' => 'fiji',
            'Papua New Guinea Football Association' => 'papua_new_guinea',
            'Solomon Islands Football Federation' => 'solomon_islands',
            'Vanuatu Football Federation' => 'vanuatu',
            'Fédération Calédonienne de Football' => 'new_caledonia',
            'Fédération Tahitienne de Football' => 'tahiti',
        ];

        return $nameToCodeMap[$associationName] ?? strtolower(str_replace(' ', '_', $associationName));
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
