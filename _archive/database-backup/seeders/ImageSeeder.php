<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use Illuminate\Support\Facades\DB;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding images for players, clubs, and associations...');
        
        $this->seedPlayerImages();
        $this->seedClubImages();
        $this->seedAssociationImages();
        
        $this->command->info('Images seeded successfully!');
    }

    private function seedPlayerImages()
    {
        $players = Player::all();
        
        foreach ($players as $player) {
            $nationality = $player->nationality ?? 'France';
            $club = $player->club?->name ?? 'Chelsea';
            
            // Images de profil basées sur la nationalité et le club
            $profileImage = $this->getPlayerProfileImage($nationality, $club);
            $flagImage = $this->getCountryFlagImage($nationality);
            
            $player->update([
                'profile_image' => $profileImage,
                'flag_image' => $flagImage
            ]);
        }
    }

    private function seedClubImages()
    {
        $clubs = Club::all();
        
        foreach ($clubs as $club) {
            $logoImage = $this->getClubLogoImage($club->name);
            $stadiumImage = $this->getStadiumImage($club->name);
            
            $club->update([
                'logo_image' => $logoImage,
                'stadium_image' => $stadiumImage
            ]);
        }
    }

    private function seedAssociationImages()
    {
        $associations = Association::all();
        
        foreach ($associations as $association) {
            $logoImage = $this->getAssociationLogoImage($association->name);
            $flagImage = $this->getCountryFlagImage($association->country ?? 'France');
            
            $association->update([
                'logo_image' => $logoImage,
                'flag_image' => $flagImage
            ]);
        }
    }

    private function getPlayerProfileImage(string $nationality, string $club): string
    {
        $nationalityImages = [
            'France' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=200&h=200&fit=crop&crop=face',
            'Brazil' => 'https://images.unsplash.com/photo-1552318965-6e6be7484ada?w=200&h=200&fit=crop&crop=face',
            'Argentina' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face',
            'Germany' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=200&fit=crop&crop=face',
            'Spain' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face',
            'Portugal' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=200&h=200&fit=crop&crop=face',
            'Netherlands' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face',
            'Italy' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=200&fit=crop&crop=face',
            'England' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face',
            'Belgium' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=200&h=200&fit=crop&crop=face'
        ];

        return $nationalityImages[$nationality] ?? $nationalityImages['France'];
    }

    private function getCountryFlagImage(string $country): string
    {
        $flagImages = [
            'France' => 'https://flagcdn.com/w80/fr.png',
            'Brazil' => 'https://flagcdn.com/w80/br.png',
            'Argentina' => 'https://flagcdn.com/w80/ar.png',
            'Germany' => 'https://flagcdn.com/w80/de.png',
            'Spain' => 'https://flagcdn.com/w80/es.png',
            'Portugal' => 'https://flagcdn.com/w80/pt.png',
            'Netherlands' => 'https://flagcdn.com/w80/nl.png',
            'Italy' => 'https://flagcdn.com/w80/it.png',
            'England' => 'https://flagcdn.com/w80/gb-eng.png',
            'Belgium' => 'https://flagcdn.com/w80/be.png',
            'Uruguay' => 'https://flagcdn.com/w80/uy.png',
            'Croatia' => 'https://flagcdn.com/w80/hr.png',
            'Morocco' => 'https://flagcdn.com/w80/ma.png',
            'Senegal' => 'https://flagcdn.com/w80/sn.png',
            'Japan' => 'https://flagcdn.com/w80/jp.png',
            'South Korea' => 'https://flagcdn.com/w80/kr.png',
            'United States' => 'https://flagcdn.com/w80/us.png',
            'Canada' => 'https://flagcdn.com/w80/ca.png',
            'Mexico' => 'https://flagcdn.com/w80/mx.png',
            'Australia' => 'https://flagcdn.com/w80/au.png'
        ];

        return $flagImages[$country] ?? $flagImages['France'];
    }

    private function getClubLogoImage(string $clubName): string
    {
        $clubLogos = [
            'Chelsea' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/Chelsea_FC.svg',
            'Manchester City' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Manchester_City_FC_badge.svg',
            'Arsenal' => 'https://upload.wikimedia.org/wikipedia/en/5/53/Arsenal_FC.svg',
            'Liverpool' => 'https://upload.wikimedia.org/wikipedia/en/0/0c/Liverpool_FC.svg',
            'Manchester United' => 'https://upload.wikimedia.org/wikipedia/en/7/7a/Manchester_United_FC_crest.svg',
            'Tottenham Hotspur' => 'https://upload.wikimedia.org/wikipedia/en/b/b4/Tottenham_Hotspur.svg',
            'West Ham United' => 'https://upload.wikimedia.org/wikipedia/en/c/c2/West_Ham_United_FC_logo.svg',
            'Aston Villa' => 'https://upload.wikimedia.org/wikipedia/en/4/47/Aston_Villa_logo.svg',
            'Newcastle United' => 'https://upload.wikimedia.org/wikipedia/en/5/56/Newcastle_United_Logo.svg',
            'Brighton & Hove Albion' => 'https://upload.wikimedia.org/wikipedia/en/f/fd/Brighton_%26_Hove_Albion_logo.svg',
            'Real Madrid' => 'https://upload.wikimedia.org/wikipedia/en/5/56/Real_Madrid_CF.svg',
            'Barcelona' => 'https://upload.wikimedia.org/wikipedia/en/4/47/FC_Barcelona_%28crest%29.svg',
            'Bayern Munich' => 'https://upload.wikimedia.org/wikipedia/commons/1/1b/FC_Bayern_M%C3%BCnchen_logo_%282017%29.svg',
            'Paris Saint-Germain' => 'https://upload.wikimedia.org/wikipedia/en/a/a7/Paris_Saint-Germain_F.C..svg',
            'Juventus' => 'https://upload.wikimedia.org/wikipedia/commons/b/bc/Juventus_Logo_2017_icon.svg',
            'AC Milan' => 'https://upload.wikimedia.org/wikipedia/commons/d/d2/AC_Milan_logo.svg',
            'Inter Milan' => 'https://upload.wikimedia.org/wikipedia/commons/0/05/FC_Internazionale_Milano_1908.svg',
            'Ajax' => 'https://upload.wikimedia.org/wikipedia/en/7/79/Ajax_Amsterdam.svg',
            'Porto' => 'https://upload.wikimedia.org/wikipedia/en/f/f1/FC_Porto.svg',
            'Benfica' => 'https://upload.wikimedia.org/wikipedia/en/a/a2/SL_Benfica_logo.svg'
        ];

        return $clubLogos[$clubName] ?? 'https://via.placeholder.com/200x200/cccccc/666666?text=' . urlencode($clubName);
    }

    private function getStadiumImage(string $clubName): string
    {
        $stadiumImages = [
            'Chelsea' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop',
            'Manchester City' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop',
            'Arsenal' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop',
            'Liverpool' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop',
            'Manchester United' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop',
            'Tottenham Hotspur' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop',
            'West Ham United' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop',
            'Aston Villa' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop',
            'Newcastle United' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop',
            'Brighton & Hove Albion' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop'
        ];

        return $stadiumImages[$clubName] ?? 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop';
    }

    private function getAssociationLogoImage(string $associationName): string
    {
        $associationLogos = [
            'The Football Association' => 'https://upload.wikimedia.org/wikipedia/en/9/9d/The_Football_Association_logo.svg',
            'Fédération Française de Football' => 'https://upload.wikimedia.org/wikipedia/fr/8/8f/F%C3%A9d%C3%A9ration_Fran%C3%A7aise_de_Football_logo.svg',
            'Deutscher Fußball-Bund' => 'https://upload.wikimedia.org/wikipedia/en/5/5c/Deutscher_Fu%C3%9Fball-Bund_logo.svg',
            'Real Federación Española de Fútbol' => 'https://upload.wikimedia.org/wikipedia/en/3/3c/Real_Federaci%C3%B3n_Espa%C3%B1ola_de_F%C3%BAtbol_logo.svg',
            'Federazione Italiana Giuoco Calcio' => 'https://upload.wikimedia.org/wikipedia/en/8/8f/Federazione_Italiana_Giuoco_Calcio_logo.svg',
            'Confederação Brasileira de Futebol' => 'https://upload.wibase_logo.svg',
            'Royal Dutch Football Association' => 'https://upload.wikimedia.org/wikipedia/en/8/8f/Koninklijke_Nederlandse_Voetbalbond_logo.svg',
            'Portuguese Football Federation' => 'https://upload.wikimedia.org/wikipedia/en/8/8f/Federa%C3%A7%C3%A3o_Portuguesa_de_Futebol_logo.svg',
            'Belgian Football Association' => 'https://upload.wikimedia.org/wikipedia/en/8/8f/Royal_Belgian_Football_Association_logo.svg'
        ];

        return $associationLogos[$associationName] ?? 'https://via.placeholder.com/200x200/cccccc/666666?text=' . urlencode($associationName);
    }
}
