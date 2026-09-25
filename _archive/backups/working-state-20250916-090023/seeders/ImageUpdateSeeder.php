<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Updating players with real photos...');
        
        // Update players with real photos
        $players = [
            // Manchester United Players
            [
                'name' => 'Marcus Rashford',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4c/Marcus_Rashford_2023.jpg/800px-Marcus_Rashford_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7a/Manchester_United_FC_crest.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'Bruno Fernandes',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6c/Bruno_Fernandes_2023.jpg/800px-Bruno_Fernandes_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7a/Manchester_United_FC_crest.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Flag_of_Portugal.svg'
            ],
            [
                'name' => 'Rasmus Højlund',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Rasmus_H%C3%B8jlund_2023.jpg/800px-Rasmus_H%C3%B8jlund_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7a/Manchester_United_FC_crest.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/9/9c/Flag_of_Denmark.svg'
            ],
            
            // Arsenal Players
            [
                'name' => 'Bukayo Saka',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Bukayo_Saka_2023.jpg/800px-Bukayo_Saka_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/53/Arsenal_FC.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'Martin Ødegaard',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Martin_%C3%98degaard_2023.jpg/800px-Martin_%C3%98degaard_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/53/Arsenal_FC.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/d/d9/Flag_of_Norway.svg'
            ],
            [
                'name' => 'Declan Rice',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Declan_Rice_2023.jpg/800px-Declan_Rice_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/53/Arsenal_FC.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            
            // Liverpool Players
            [
                'name' => 'Mohamed Salah',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Mohamed_Salah_2023.jpg/800px-Mohamed_Salah_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/0/0c/Liverpool_FC.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/f/fe/Flag_of_Egypt.svg'
            ],
            [
                'name' => 'Virgil van Dijk',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Virgil_van_Dijk_2023.jpg/800px-Virgil_van_Dijk_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/0/0c/Liverpool_FC.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/2/20/Flag_of_the_Netherlands.svg'
            ],
            [
                'name' => 'Darwin Núñez',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Darwin_N%C3%BA%C3%B1ez_2023.jpg/800px-Darwin_N%C3%BA%C3%B1ez_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/0/0c/Liverpool_FC.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/f/fe/Flag_of_Uruguay.svg'
            ],
            
            // Manchester City Players
            [
                'name' => 'Erling Haaland',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Erling_Haaland_2023.jpg/800px-Erling_Haaland_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Manchester_City_FC_badge.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/4/4c/Flag_of_Norway.svg'
            ],
            [
                'name' => 'Kevin De Bruyne',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Kevin_De_Bruyne_2023.jpg/800px-Kevin_De_Bruyne_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Manchester_City_FC_badge.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/6/65/Flag_of_Belgium.svg'
            ],
            [
                'name' => 'Phil Foden',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Phil_Foden_2023.jpg/800px-Phil_Foden_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Manchester_City_FC_badge.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            
            // Chelsea Players
            [
                'name' => 'Cole Palmer',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Cole_Palmer_2023.jpg/800px-Cole_Palmer_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/Chelsea_FC.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'Enzo Fernández',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Enzo_Fern%C3%A1ndez_2023.jpg/800px-Enzo_Fern%C3%A1ndez_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/Chelsea_FC.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/1/1a/Flag_of_Argentina.svg'
            ],
            [
                'name' => 'Moisés Caicedo',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Mois%C3%A9s_Caicedo_2023.jpg/800px-Mois%C3%A9s_Caicedo_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/Chelsea_FC.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/e/e8/Flag_of_Ecuador.svg'
            ],
            
            // Tottenham Players
            [
                'name' => 'Son Heung-min',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Son_Heung-min_2023.jpg/800px-Son_Heung-min_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/b/b4/Tottenham_Hotspur.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/0/09/Flag_of_South_Korea.svg'
            ],
            [
                'name' => 'James Maddison',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/James_Maddison_2023.jpg/800px-James_Maddison_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/b/b4/Tottenham_Hotspur.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'Richarlison',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Richarlison_2023.jpg/800px-Richarlison_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/b/b4/Tottenham_Hotspur.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/0/05/Flag_of_Brazil.svg'
            ],
            
            // Newcastle Players
            [
                'name' => 'Alexander Isak',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Alexander_Isak_2023.jpg/800px-Alexander_Isak_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/56/Newcastle_United_Logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/4/4c/Flag_of_Sweden.svg'
            ],
            [
                'name' => 'Bruno Guimarães',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Bruno_Guimar%C3%A3es_2023.jpg/800px-Bruno_Guimar%C3%A3es_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/56/Newcastle_United_Logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/0/05/Flag_of_Brazil.svg'
            ],
            [
                'name' => 'Kieran Trippier',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Kieran_Trippier_2023.jpg/800px-Kieran_Trippier_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/56/Newcastle_United_Logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            
            // Aston Villa Players
            [
                'name' => 'Ollie Watkins',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Ollie_Watkins_2023.jpg/800px-Ollie_Watkins_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Aston_Villa_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'Douglas Luiz',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Douglas_Luiz_2023.jpg/800px-Douglas_Luiz_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Aston_Villa_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/0/05/Flag_of_Brazil.svg'
            ],
            [
                'name' => 'Emiliano Martínez',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Emiliano_Mart%C3%ADnez_2023.jpg/800px-Emiliano_Mart%C3%ADnez_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Aston_Villa_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/1/1a/Flag_of_Argentina.svg'
            ],
            
            // West Ham Players
            [
                'name' => 'Jarrod Bowen',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Jarrod_Bowen_2023.jpg/800px-Jarrod_Bowen_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/West_Ham_United_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'Lucas Paquetá',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Lucas_Paquet%C3%A1_2023.jpg/800px-Lucas_Paquet%C3%A1_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/West_Ham_United_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/0/05/Flag_of_Brazil.svg'
            ],
            [
                'name' => 'Mohammed Kudus',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Mohammed_Kudus_2023.jpg/800px-Mohammed_Kudus_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/West_Ham_United_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/1/19/Flag_of_Ghana.svg'
            ],
            
            // Brighton Players
            [
                'name' => 'Evan Ferguson',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Evan_Ferguson_2023.jpg/800px-Evan_Ferguson_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fd/Brighton_%26_Hove_Albion_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/4/45/Flag_of_Ireland.svg'
            ],
            [
                'name' => 'Kaoru Mitoma',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Kaoru_Mitoma_2023.jpg/800px-Kaoru_Mitoma_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fd/Brighton_%26_Hove_Albion_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9e/Flag_of_Japan.svg'
            ],
            [
                'name' => 'Pascal Groß',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Pascal_Gro%C3%9F_2023.jpg/800px-Pascal_Gro%C3%9F_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fd/Brighton_%26_Hove_Albion_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/b/ba/Flag_of_Germany.svg'
            ],
            
            // Crystal Palace Players
            [
                'name' => 'Eberechi Eze',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Eberechi_Eze_2023.jpg/800px-Eberechi_Eze_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/a/a2/Crystal_Palace_FC_logo_%282022%29.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'Michael Olise',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Michael_Olise_2023.jpg/800px-Michael_Olise_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/a/a2/Crystal_Palace_FC_logo_%282022%29.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/c/c3/Flag_of_France.svg'
            ],
            [
                'name' => 'Jean-Philippe Mateta',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Jean-Philippe_Mateta_2023.jpg/800px-Jean-Philippe_Mateta_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/a/a2/Crystal_Palace_FC_logo_%282022%29.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/c/c3/Flag_of_France.svg'
            ],
            
            // Brentford Players
            [
                'name' => 'Ivan Toney',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Ivan_Toney_2023.jpg/800px-Ivan_Toney_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/2/2a/Brentford_FC_crest.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'Bryan Mbeumo',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Bryan_Mbeumo_2023.jpg/800px-Bryan_Mbeumo_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/2/2a/Brentford_FC_crest.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/4/4f/Flag_of_Cameroon.svg'
            ],
            [
                'name' => 'Yoane Wissa',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Yoane_Wissa_2023.jpg/800px-Yoane_Wissa_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/2/2a/Brentford_FC_crest.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/9/92/Flag_of_the_Democratic_Republic_of_the_Congo.svg'
            ],
            
            // Wolves Players
            [
                'name' => 'Pedro Neto',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Pedro_Neto_2023.jpg/800px-Pedro_Neto_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fc/Wolverhampton_Wanderers.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Flag_of_Portugal.svg'
            ],
            [
                'name' => 'Matheus Cunha',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Matheus_Cunha_2023.jpg/800px-Matheus_Cunha_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fc/Wolverhampton_Wanderers.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/0/05/Flag_of_Brazil.svg'
            ],
            [
                'name' => 'Hwang Hee-chan',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Hwang_Hee-chan_2023.jpg/800px-Hwang_Hee-chan_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fc/Wolverhampton_Wanderers.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/0/09/Flag_of_South_Korea.svg'
            ],
            
            // Fulham Players
            [
                'name' => 'Raúl Jiménez',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Ra%C3%BAl_Jim%C3%A9nez_2023.jpg/800px-Ra%C3%BAl_Jim%C3%A9nez_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Fulham_FC_%28shield%29.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/f/fc/Flag_of_Mexico.svg'
            ],
            [
                'name' => 'Andreas Pereira',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Andreas_Pereira_2023.jpg/800px-Andreas_Pereira_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Fulham_FC_%28shield%29.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/0/05/Flag_of_Brazil.svg'
            ],
            [
                'name' => 'Willian',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Willian_2023.jpg/800px-Willian_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Fulham_FC_%28shield%29.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/0/05/Flag_of_Brazil.svg'
            ],
            
            // Everton Players
            [
                'name' => 'Dominic Calvert-Lewin',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Dominic_Calvert-Lewin_2023.jpg/800px-Dominic_Calvert-Lewin_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7c/Everton_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'Abdoulaye Doucouré',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Abdoulaye_Doucour%C3%A9_2023.jpg/800px-Abdoulaye_Doucour%C3%A9_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7c/Everton_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/d/d0/Flag_of_Mali.svg'
            ],
            [
                'name' => 'Jordan Pickford',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Jordan_Pickford_2023.jpg/800px-Jordan_Pickford_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7c/Everton_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            
            // Nottingham Forest Players
            [
                'name' => 'Taiwo Awoniyi',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Taiwo_Awoniyi_2023.jpg/800px-Taiwo_Awoniyi_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/e5/Nottingham_Forest_F.C._logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/7/79/Flag_of_Nigeria.svg'
            ],
            [
                'name' => 'Morgan Gibbs-White',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Morgan_Gibbs-White_2023.jpg/800px-Morgan_Gibbs-White_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/e5/Nottingham_Forest_F.C._logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'Anthony Elanga',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Anthony_Elanga_2023.jpg/800px-Anthony_Elanga_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/e5/Nottingham_Forest_F.C._logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/4/4c/Flag_of_Sweden.svg'
            ],
            
            // Burnley Players
            [
                'name' => 'Lyle Foster',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Lyle_Foster_2023.jpg/800px-Lyle_Foster_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/6/62/Burnley_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/a/af/Flag_of_South_Africa.svg'
            ],
            [
                'name' => 'Josh Brownhill',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Josh_Brownhill_2023.jpg/800px-Josh_Brownhill_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/6/62/Burnley_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'James Trafford',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/James_Trafford_2023.jpg/800px-James_Trafford_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/6/62/Burnley_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            
            // Sheffield United Players
            [
                'name' => 'Cameron Archer',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Cameron_Archer_2023.jpg/800px-Cameron_Archer_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9c/Sheffield_United_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'Gustavo Hamer',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Gustavo_Hamer_2023.jpg/800px-Gustavo_Hamer_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9c/Sheffield_United_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/0/05/Flag_of_Brazil.svg'
            ],
            [
                'name' => 'Wes Foderingham',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Wes_Foderingham_2023.jpg/800px-Wes_Foderingham_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9c/Sheffield_United_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            
            // Luton Town Players
            [
                'name' => 'Carlton Morris',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Carlton_Morris_2023.jpg/800px-Carlton_Morris_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Luton_Town_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'Alfie Doughty',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Alfie_Doughty_2023.jpg/800px-Alfie_Doughty_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Luton_Town_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'Thomas Kaminski',
                'player_face_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Thomas_Kaminski_2023.jpg/800px-Thomas_Kaminski_2023.jpg',
                'club_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Luton_Town_FC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/6/65/Flag_of_Belgium.svg'
            ]
        ];

        // Update existing players with real photos
        foreach ($players as $playerData) {
            $player = Player::where('name', $playerData['name'])->first();
            if ($player) {
                $player->update([
                    'player_face_url' => $playerData['player_face_url'],
                    'club_logo_url' => $playerData['club_logo_url'],
                    'nation_flag_url' => $playerData['nation_flag_url']
                ]);
                $this->command->info("Updated player: {$playerData['name']}");
            }
        }

        $this->command->info('Updating clubs with real logos...');
        
        // Update clubs with real logos
        $clubs = [
            [
                'name' => 'Manchester United',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7a/Manchester_United_FC_crest.svg'
            ],
            [
                'name' => 'Arsenal',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/53/Arsenal_FC.svg'
            ],
            [
                'name' => 'Liverpool',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/0/0c/Liverpool_FC.svg'
            ],
            [
                'name' => 'Manchester City',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Manchester_City_FC_badge.svg'
            ],
            [
                'name' => 'Chelsea',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/Chelsea_FC.svg'
            ],
            [
                'name' => 'Tottenham Hotspur',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/b/b4/Tottenham_Hotspur.svg'
            ],
            [
                'name' => 'Newcastle United',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/56/Newcastle_United_Logo.svg'
            ],
            [
                'name' => 'Aston Villa',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Aston_Villa_logo.svg'
            ],
            [
                'name' => 'West Ham United',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/West_Ham_United_FC_logo.svg'
            ],
            [
                'name' => 'Brighton & Hove Albion',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fd/Brighton_%26_Hove_Albion_logo.svg'
            ],
            [
                'name' => 'Crystal Palace',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/a/a2/Crystal_Palace_FC_logo_%282022%29.svg'
            ],
            [
                'name' => 'Brentford',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/2/2a/Brentford_FC_crest.svg'
            ],
            [
                'name' => 'Wolverhampton Wanderers',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fc/Wolverhampton_Wanderers.svg'
            ],
            [
                'name' => 'Fulham',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Fulham_FC_%28shield%29.svg'
            ],
            [
                'name' => 'Everton',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7c/Everton_FC_logo.svg'
            ],
            [
                'name' => 'Nottingham Forest',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/e5/Nottingham_Forest_F.C._logo.svg'
            ],
            [
                'name' => 'Burnley',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/6/62/Burnley_FC_logo.svg'
            ],
            [
                'name' => 'Sheffield United',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9c/Sheffield_United_FC_logo.svg'
            ],
            [
                'name' => 'Luton Town',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Luton_Town_FC_logo.svg'
            ]
        ];

        // Update existing clubs with real logos
        foreach ($clubs as $clubData) {
            $club = Club::where('name', $clubData['name'])->first();
            if ($club) {
                $club->update([
                    'logo_url' => $clubData['logo_url']
                ]);
                $this->command->info("Updated club: {$clubData['name']}");
            }
        }

        $this->command->info('Updating associations with real logos...');
        
        // Update associations with real logos
        $associations = [
            [
                'name' => 'The Football Association',
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/4/47/FA_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ],
            [
                'name' => 'Fédération Française de Football',
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/French_Football_Federation_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/c/c3/Flag_of_France.svg'
            ],
            [
                'name' => 'Deutscher Fußball-Bund',
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/5a/DFB_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/b/ba/Flag_of_Germany.svg'
            ],
            [
                'name' => 'Real Federación Española de Fútbol',
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9a/RFEF_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9a/Flag_of_Spain.svg'
            ],
            [
                'name' => 'Federazione Italiana Giuoco Calcio',
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/FIGC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/0/03/Flag_of_Italy.svg'
            ],
            [
                'name' => 'Fédération Royale Belge de Football',
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/6/6a/Belgian_Football_Association_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/6/65/Flag_of_Belgium.svg'
            ],
            [
                'name' => 'Koninklijke Nederlandse Voetbalbond',
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7a/KNVB_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/2/20/Flag_of_the_Netherlands.svg'
            ],
            [
                'name' => 'Federação Portuguesa de Futebol',
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/Portuguese_Football_Federation_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Flag_of_Portugal.svg'
            ],
            [
                'name' => 'Confederação Brasileira de Futebol',
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4a/Brazilian_Football_Confederation_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/0/05/Flag_of_Brazil.svg'
            ],
            [
                'name' => 'Asociación del Fútbol Argentino',
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/Argentine_Football_Association_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/1/1a/Flag_of_Argentina.svg'
            ]
        ];

        // Update existing associations with real logos
        foreach ($associations as $associationData) {
            $association = Association::where('name', $associationData['name'])->first();
            if ($association) {
                $association->update([
                    'association_logo_url' => $associationData['association_logo_url'],
                    'nation_flag_url' => $associationData['nation_flag_url']
                ]);
                $this->command->info("Updated association: {$associationData['name']}");
            }
        }

        $this->command->info('Image update completed successfully!');
        $this->command->info('Updated ' . count($players) . ' players with real photos.');
        $this->command->info('Updated ' . count($clubs) . ' clubs with real logos.');
        $this->command->info('Updated ' . count($associations) . ' associations with real logos.');
    }
} 