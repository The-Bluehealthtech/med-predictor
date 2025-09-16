<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Player;
use App\Models\Competition;
use App\Models\Club;
use App\Models\Association;

class PremierLeagueTeamsSeeder extends Seeder
{
    public function run(): void
    {
        // Get the Premier League competition
        $competition = Competition::where('name', 'Premier League')->first();
        if (!$competition) {
            $this->command->error('Premier League competition not found!');
            return;
        }

        // Get the first club (or create one if needed)
        $club = Club::first();
        if (!$club) {
            $club = Club::create([
                'name' => 'Premier League Club',
                'association_id' => Association::first()->id,
                'logo' => 'defaults/club-logo.png'
            ]);
        }

        $teams = [
            [
                'name' => 'Arsenal',
                'short_name' => 'ARS',
                'real_players' => [
                    ['name' => 'David Raya', 'position' => 'GK', 'nationality' => 'Spain', 'jersey_number' => 1],
                    ['name' => 'William Saliba', 'position' => 'DEF', 'nationality' => 'France', 'jersey_number' => 2],
                    ['name' => 'Gabriel Magalhães', 'position' => 'DEF', 'nationality' => 'Brazil', 'jersey_number' => 6],
                    ['name' => 'Ben White', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 4],
                    ['name' => 'Oleksandr Zinchenko', 'position' => 'DEF', 'nationality' => 'Ukraine', 'jersey_number' => 35],
                    ['name' => 'Declan Rice', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 41],
                    ['name' => 'Martin Ødegaard', 'position' => 'MID', 'nationality' => 'Norway', 'jersey_number' => 8],
                    ['name' => 'Bukayo Saka', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 7],
                    ['name' => 'Gabriel Martinelli', 'position' => 'FWD', 'nationality' => 'Brazil', 'jersey_number' => 11],
                    ['name' => 'Kai Havertz', 'position' => 'FWD', 'nationality' => 'Germany', 'jersey_number' => 29],
                    ['name' => 'Gabriel Jesus', 'position' => 'FWD', 'nationality' => 'Brazil', 'jersey_number' => 9],
                ]
            ],
            [
                'name' => 'Aston Villa',
                'short_name' => 'AVL',
                'real_players' => [
                    ['name' => 'Emiliano Martínez', 'position' => 'GK', 'nationality' => 'Argentina', 'jersey_number' => 1],
                    ['name' => 'Matty Cash', 'position' => 'DEF', 'nationality' => 'Poland', 'jersey_number' => 2],
                    ['name' => 'Diego Carlos', 'position' => 'DEF', 'nationality' => 'Brazil', 'jersey_number' => 3],
                    ['name' => 'Pau Torres', 'position' => 'DEF', 'nationality' => 'Spain', 'jersey_number' => 14],
                    ['name' => 'Lucas Digne', 'position' => 'DEF', 'nationality' => 'France', 'jersey_number' => 12],
                    ['name' => 'Douglas Luiz', 'position' => 'MID', 'nationality' => 'Brazil', 'jersey_number' => 6],
                    ['name' => 'John McGinn', 'position' => 'MID', 'nationality' => 'Scotland', 'jersey_number' => 7],
                    ['name' => 'Leon Bailey', 'position' => 'MID', 'nationality' => 'Jamaica', 'jersey_number' => 31],
                    ['name' => 'Moussa Diaby', 'position' => 'FWD', 'nationality' => 'France', 'jersey_number' => 19],
                    ['name' => 'Ollie Watkins', 'position' => 'FWD', 'nationality' => 'England', 'jersey_number' => 11],
                ]
            ],
            [
                'name' => 'Bournemouth',
                'short_name' => 'BOU',
                'real_players' => [
                    ['name' => 'Neto', 'position' => 'GK', 'nationality' => 'Brazil', 'jersey_number' => 1],
                    ['name' => 'Adam Smith', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 2],
                    ['name' => 'Lloyd Kelly', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 5],
                    ['name' => 'Marcos Senesi', 'position' => 'DEF', 'nationality' => 'Argentina', 'jersey_number' => 25],
                    ['name' => 'Milos Kerkez', 'position' => 'DEF', 'nationality' => 'Hungary', 'jersey_number' => 3],
                    ['name' => 'Lewis Cook', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 16],
                    ['name' => 'Philip Billing', 'position' => 'MID', 'nationality' => 'Denmark', 'jersey_number' => 29],
                    ['name' => 'Ryan Christie', 'position' => 'MID', 'nationality' => 'Scotland', 'jersey_number' => 10],
                    ['name' => 'Justin Kluivert', 'position' => 'FWD', 'nationality' => 'Netherlands', 'jersey_number' => 19],
                    ['name' => 'Dominic Solanke', 'position' => 'FWD', 'nationality' => 'England', 'jersey_number' => 9],
                ]
            ],
            [
                'name' => 'Brentford',
                'short_name' => 'BRE',
                'real_players' => [
                    ['name' => 'Mark Flekken', 'position' => 'GK', 'nationality' => 'Netherlands', 'jersey_number' => 1],
                    ['name' => 'Aaron Hickey', 'position' => 'DEF', 'nationality' => 'Scotland', 'jersey_number' => 2],
                    ['name' => 'Ethan Pinnock', 'position' => 'DEF', 'nationality' => 'Jamaica', 'jersey_number' => 5],
                    ['name' => 'Nathan Collins', 'position' => 'DEF', 'nationality' => 'Ireland', 'jersey_number' => 22],
                    ['name' => 'Rico Henry', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 3],
                    ['name' => 'Christian Nørgaard', 'position' => 'MID', 'nationality' => 'Denmark', 'jersey_number' => 6],
                    ['name' => 'Mathias Jensen', 'position' => 'MID', 'nationality' => 'Denmark', 'jersey_number' => 8],
                    ['name' => 'Bryan Mbeumo', 'position' => 'MID', 'nationality' => 'Cameroon', 'jersey_number' => 19],
                    ['name' => 'Yoane Wissa', 'position' => 'FWD', 'nationality' => 'DR Congo', 'jersey_number' => 11],
                    ['name' => 'Ivan Toney', 'position' => 'FWD', 'nationality' => 'England', 'jersey_number' => 17],
                ]
            ],
            [
                'name' => 'Brighton & Hove Albion',
                'short_name' => 'BHA',
                'real_players' => [
                    ['name' => 'Bart Verbruggen', 'position' => 'GK', 'nationality' => 'Netherlands', 'jersey_number' => 1],
                    ['name' => 'Joël Veltman', 'position' => 'DEF', 'nationality' => 'Netherlands', 'jersey_number' => 34],
                    ['name' => 'Lewis Dunk', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 5],
                    ['name' => 'Jan Paul van Hecke', 'position' => 'DEF', 'nationality' => 'Netherlands', 'jersey_number' => 29],
                    ['name' => 'Pervis Estupiñán', 'position' => 'DEF', 'nationality' => 'Ecuador', 'jersey_number' => 30],
                    ['name' => 'Billy Gilmour', 'position' => 'MID', 'nationality' => 'Scotland', 'jersey_number' => 27],
                    ['name' => 'Pascal Groß', 'position' => 'MID', 'nationality' => 'Germany', 'jersey_number' => 13],
                    ['name' => 'Kaoru Mitoma', 'position' => 'MID', 'nationality' => 'Japan', 'jersey_number' => 22],
                    ['name' => 'Simon Adingra', 'position' => 'FWD', 'nationality' => 'Ivory Coast', 'jersey_number' => 24],
                    ['name' => 'Evan Ferguson', 'position' => 'FWD', 'nationality' => 'Ireland', 'jersey_number' => 28],
                ]
            ],
            [
                'name' => 'Burnley',
                'short_name' => 'BUR',
                'real_players' => [
                    ['name' => 'James Trafford', 'position' => 'GK', 'nationality' => 'England', 'jersey_number' => 1],
                    ['name' => 'Connor Roberts', 'position' => 'DEF', 'nationality' => 'Wales', 'jersey_number' => 14],
                    ['name' => 'Dara O\'Shea', 'position' => 'DEF', 'nationality' => 'Ireland', 'jersey_number' => 2],
                    ['name' => 'Jordan Beyer', 'position' => 'DEF', 'nationality' => 'Germany', 'jersey_number' => 5],
                    ['name' => 'Charlie Taylor', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 3],
                    ['name' => 'Josh Cullen', 'position' => 'MID', 'nationality' => 'Ireland', 'jersey_number' => 24],
                    ['name' => 'Sander Berge', 'position' => 'MID', 'nationality' => 'Norway', 'jersey_number' => 16],
                    ['name' => 'Wilson Odobert', 'position' => 'MID', 'nationality' => 'France', 'jersey_number' => 47],
                    ['name' => 'Lyle Foster', 'position' => 'FWD', 'nationality' => 'South Africa', 'jersey_number' => 17],
                    ['name' => 'Zeki Amdouni', 'position' => 'FWD', 'nationality' => 'Switzerland', 'jersey_number' => 25],
                ]
            ],
            [
                'name' => 'Chelsea',
                'short_name' => 'CHE',
                'real_players' => [
                    ['name' => 'Robert Sánchez', 'position' => 'GK', 'nationality' => 'Spain', 'jersey_number' => 1],
                    ['name' => 'Reece James', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 24],
                    ['name' => 'Thiago Silva', 'position' => 'DEF', 'nationality' => 'Brazil', 'jersey_number' => 6],
                    ['name' => 'Levi Colwill', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 26],
                    ['name' => 'Ben Chilwell', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 21],
                    ['name' => 'Moisés Caicedo', 'position' => 'MID', 'nationality' => 'Ecuador', 'jersey_number' => 25],
                    ['name' => 'Enzo Fernández', 'position' => 'MID', 'nationality' => 'Argentina', 'jersey_number' => 8],
                    ['name' => 'Cole Palmer', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 20],
                    ['name' => 'Raheem Sterling', 'position' => 'FWD', 'nationality' => 'England', 'jersey_number' => 7],
                    ['name' => 'Nicolas Jackson', 'position' => 'FWD', 'nationality' => 'Senegal', 'jersey_number' => 15],
                ]
            ],
            [
                'name' => 'Crystal Palace',
                'short_name' => 'CRY',
                'real_players' => [
                    ['name' => 'Sam Johnstone', 'position' => 'GK', 'nationality' => 'England', 'jersey_number' => 1],
                    ['name' => 'Joel Ward', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 2],
                    ['name' => 'Joachim Andersen', 'position' => 'DEF', 'nationality' => 'Denmark', 'jersey_number' => 16],
                    ['name' => 'Marc Guéhi', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 6],
                    ['name' => 'Tyrick Mitchell', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 3],
                    ['name' => 'Cheick Doucouré', 'position' => 'MID', 'nationality' => 'Mali', 'jersey_number' => 28],
                    ['name' => 'Jefferson Lerma', 'position' => 'MID', 'nationality' => 'Colombia', 'jersey_number' => 8],
                    ['name' => 'Eberechi Eze', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 10],
                    ['name' => 'Michael Olise', 'position' => 'FWD', 'nationality' => 'France', 'jersey_number' => 7],
                    ['name' => 'Jean-Philippe Mateta', 'position' => 'FWD', 'nationality' => 'France', 'jersey_number' => 14],
                ]
            ],
            [
                'name' => 'Everton',
                'short_name' => 'EVE',
                'real_players' => [
                    ['name' => 'Jordan Pickford', 'position' => 'GK', 'nationality' => 'England', 'jersey_number' => 1],
                    ['name' => 'Seamus Coleman', 'position' => 'DEF', 'nationality' => 'Ireland', 'jersey_number' => 23],
                    ['name' => 'James Tarkowski', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 6],
                    ['name' => 'Jarrad Branthwaite', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 32],
                    ['name' => 'Vitaliy Mykolenko', 'position' => 'DEF', 'nationality' => 'Ukraine', 'jersey_number' => 19],
                    ['name' => 'Amadou Onana', 'position' => 'MID', 'nationality' => 'Belgium', 'jersey_number' => 8],
                    ['name' => 'James Garner', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 37],
                    ['name' => 'Dwight McNeil', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 7],
                    ['name' => 'Jack Harrison', 'position' => 'FWD', 'nationality' => 'England', 'jersey_number' => 11],
                    ['name' => 'Dominic Calvert-Lewin', 'position' => 'FWD', 'nationality' => 'England', 'jersey_number' => 9],
                ]
            ],
            [
                'name' => 'Fulham',
                'short_name' => 'FUL',
                'real_players' => [
                    ['name' => 'Bernd Leno', 'position' => 'GK', 'nationality' => 'Germany', 'jersey_number' => 17],
                    ['name' => 'Kenny Tete', 'position' => 'DEF', 'nationality' => 'Netherlands', 'jersey_number' => 2],
                    ['name' => 'Tosin Adarabioyo', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 4],
                    ['name' => 'Calvin Bassey', 'position' => 'DEF', 'nationality' => 'Nigeria', 'jersey_number' => 3],
                    ['name' => 'Antonee Robinson', 'position' => 'DEF', 'nationality' => 'USA', 'jersey_number' => 33],
                    ['name' => 'João Palhinha', 'position' => 'MID', 'nationality' => 'Portugal', 'jersey_number' => 26],
                    ['name' => 'Andreas Pereira', 'position' => 'MID', 'nationality' => 'Brazil', 'jersey_number' => 18],
                    ['name' => 'Willian', 'position' => 'MID', 'nationality' => 'Brazil', 'jersey_number' => 20],
                    ['name' => 'Bobby De Cordova-Reid', 'position' => 'FWD', 'nationality' => 'Jamaica', 'jersey_number' => 14],
                    ['name' => 'Raúl Jiménez', 'position' => 'FWD', 'nationality' => 'Mexico', 'jersey_number' => 7],
                ]
            ],
            [
                'name' => 'Liverpool',
                'short_name' => 'LIV',
                'real_players' => [
                    ['name' => 'Alisson', 'position' => 'GK', 'nationality' => 'Brazil', 'jersey_number' => 1],
                    ['name' => 'Trent Alexander-Arnold', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 66],
                    ['name' => 'Virgil van Dijk', 'position' => 'DEF', 'nationality' => 'Netherlands', 'jersey_number' => 4],
                    ['name' => 'Ibrahima Konaté', 'position' => 'DEF', 'nationality' => 'France', 'jersey_number' => 5],
                    ['name' => 'Andy Robertson', 'position' => 'DEF', 'nationality' => 'Scotland', 'jersey_number' => 26],
                    ['name' => 'Alexis Mac Allister', 'position' => 'MID', 'nationality' => 'Argentina', 'jersey_number' => 10],
                    ['name' => 'Dominik Szoboszlai', 'position' => 'MID', 'nationality' => 'Hungary', 'jersey_number' => 8],
                    ['name' => 'Mohamed Salah', 'position' => 'MID', 'nationality' => 'Egypt', 'jersey_number' => 11],
                    ['name' => 'Luis Díaz', 'position' => 'FWD', 'nationality' => 'Colombia', 'jersey_number' => 7],
                    ['name' => 'Darwin Núñez', 'position' => 'FWD', 'nationality' => 'Uruguay', 'jersey_number' => 9],
                ]
            ],
            [
                'name' => 'Luton Town',
                'short_name' => 'LUT',
                'real_players' => [
                    ['name' => 'Thomas Kaminski', 'position' => 'GK', 'nationality' => 'Belgium', 'jersey_number' => 1],
                    ['name' => 'Teden Mengi', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 4],
                    ['name' => 'Tom Lockyer', 'position' => 'DEF', 'nationality' => 'Wales', 'jersey_number' => 15],
                    ['name' => 'Amari\'i Bell', 'position' => 'DEF', 'nationality' => 'Jamaica', 'jersey_number' => 29],
                    ['name' => 'Alfie Doughty', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 45],
                    ['name' => 'Ross Barkley', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 6],
                    ['name' => 'Albert Sambi Lokonga', 'position' => 'MID', 'nationality' => 'Belgium', 'jersey_number' => 28],
                    ['name' => 'Chiedozie Ogbene', 'position' => 'MID', 'nationality' => 'Ireland', 'jersey_number' => 7],
                    ['name' => 'Carlton Morris', 'position' => 'FWD', 'nationality' => 'England', 'jersey_number' => 9],
                    ['name' => 'Elijah Adebayo', 'position' => 'FWD', 'nationality' => 'England', 'jersey_number' => 11],
                ]
            ],
            [
                'name' => 'Manchester City',
                'short_name' => 'MCI',
                'real_players' => [
                    ['name' => 'Ederson', 'position' => 'GK', 'nationality' => 'Brazil', 'jersey_number' => 31],
                    ['name' => 'Kyle Walker', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 2],
                    ['name' => 'Rúben Dias', 'position' => 'DEF', 'nationality' => 'Portugal', 'jersey_number' => 3],
                    ['name' => 'John Stones', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 5],
                    ['name' => 'Josko Gvardiol', 'position' => 'DEF', 'nationality' => 'Croatia', 'jersey_number' => 24],
                    ['name' => 'Rodri', 'position' => 'MID', 'nationality' => 'Spain', 'jersey_number' => 16],
                    ['name' => 'Kevin De Bruyne', 'position' => 'MID', 'nationality' => 'Belgium', 'jersey_number' => 17],
                    ['name' => 'Phil Foden', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 47],
                    ['name' => 'Jack Grealish', 'position' => 'FWD', 'nationality' => 'England', 'jersey_number' => 10],
                    ['name' => 'Erling Haaland', 'position' => 'FWD', 'nationality' => 'Norway', 'jersey_number' => 9],
                ]
            ],
            [
                'name' => 'Manchester United',
                'short_name' => 'MUN',
                'real_players' => [
                    ['name' => 'André Onana', 'position' => 'GK', 'nationality' => 'Cameroon', 'jersey_number' => 24],
                    ['name' => 'Diogo Dalot', 'position' => 'DEF', 'nationality' => 'Portugal', 'jersey_number' => 20],
                    ['name' => 'Raphaël Varane', 'position' => 'DEF', 'nationality' => 'France', 'jersey_number' => 19],
                    ['name' => 'Lisandro Martínez', 'position' => 'DEF', 'nationality' => 'Argentina', 'jersey_number' => 6],
                    ['name' => 'Luke Shaw', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 23],
                    ['name' => 'Casemiro', 'position' => 'MID', 'nationality' => 'Brazil', 'jersey_number' => 18],
                    ['name' => 'Bruno Fernandes', 'position' => 'MID', 'nationality' => 'Portugal', 'jersey_number' => 8],
                    ['name' => 'Marcus Rashford', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 10],
                    ['name' => 'Alejandro Garnacho', 'position' => 'FWD', 'nationality' => 'Argentina', 'jersey_number' => 17],
                    ['name' => 'Rasmus Højlund', 'position' => 'FWD', 'nationality' => 'Denmark', 'jersey_number' => 11],
                ]
            ],
            [
                'name' => 'Newcastle United',
                'short_name' => 'NEW',
                'real_players' => [
                    ['name' => 'Nick Pope', 'position' => 'GK', 'nationality' => 'England', 'jersey_number' => 22],
                    ['name' => 'Kieran Trippier', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 2],
                    ['name' => 'Fabian Schär', 'position' => 'DEF', 'nationality' => 'Switzerland', 'jersey_number' => 5],
                    ['name' => 'Sven Botman', 'position' => 'DEF', 'nationality' => 'Netherlands', 'jersey_number' => 4],
                    ['name' => 'Dan Burn', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 33],
                    ['name' => 'Bruno Guimarães', 'position' => 'MID', 'nationality' => 'Brazil', 'jersey_number' => 39],
                    ['name' => 'Joelinton', 'position' => 'MID', 'nationality' => 'Brazil', 'jersey_number' => 7],
                    ['name' => 'Anthony Gordon', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 10],
                    ['name' => 'Miguel Almirón', 'position' => 'FWD', 'nationality' => 'Paraguay', 'jersey_number' => 24],
                    ['name' => 'Alexander Isak', 'position' => 'FWD', 'nationality' => 'Sweden', 'jersey_number' => 14],
                ]
            ],
            [
                'name' => 'Nottingham Forest',
                'short_name' => 'NFO',
                'real_players' => [
                    ['name' => 'Matt Turner', 'position' => 'GK', 'nationality' => 'USA', 'jersey_number' => 1],
                    ['name' => 'Neco Williams', 'position' => 'DEF', 'nationality' => 'Wales', 'jersey_number' => 7],
                    ['name' => 'Willy Boly', 'position' => 'DEF', 'nationality' => 'Ivory Coast', 'jersey_number' => 30],
                    ['name' => 'Murillo', 'position' => 'DEF', 'nationality' => 'Brazil', 'jersey_number' => 40],
                    ['name' => 'Ola Aina', 'position' => 'DEF', 'nationality' => 'Nigeria', 'jersey_number' => 43],
                    ['name' => 'Ibrahim Sangaré', 'position' => 'MID', 'nationality' => 'Ivory Coast', 'jersey_number' => 6],
                    ['name' => 'Morgan Gibbs-White', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 10],
                    ['name' => 'Callum Hudson-Odoi', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 14],
                    ['name' => 'Anthony Elanga', 'position' => 'FWD', 'nationality' => 'Sweden', 'jersey_number' => 21],
                    ['name' => 'Taiwo Awoniyi', 'position' => 'FWD', 'nationality' => 'Nigeria', 'jersey_number' => 9],
                ]
            ],
            [
                'name' => 'Sheffield United',
                'short_name' => 'SHU',
                'real_players' => [
                    ['name' => 'Wes Foderingham', 'position' => 'GK', 'nationality' => 'England', 'jersey_number' => 18],
                    ['name' => 'Jayden Bogle', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 20],
                    ['name' => 'Anel Ahmedhodžić', 'position' => 'DEF', 'nationality' => 'Bosnia', 'jersey_number' => 15],
                    ['name' => 'Jack Robinson', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 19],
                    ['name' => 'Max Lowe', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 13],
                    ['name' => 'Vinícius Souza', 'position' => 'MID', 'nationality' => 'Brazil', 'jersey_number' => 16],
                    ['name' => 'Oliver Norwood', 'position' => 'MID', 'nationality' => 'Northern Ireland', 'jersey_number' => 16],
                    ['name' => 'James McAtee', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 28],
                    ['name' => 'Cameron Archer', 'position' => 'FWD', 'nationality' => 'England', 'jersey_number' => 10],
                    ['name' => 'Oli McBurnie', 'position' => 'FWD', 'nationality' => 'Scotland', 'jersey_number' => 9],
                ]
            ],
            [
                'name' => 'Tottenham Hotspur',
                'short_name' => 'TOT',
                'real_players' => [
                    ['name' => 'Guglielmo Vicario', 'position' => 'GK', 'nationality' => 'Italy', 'jersey_number' => 13],
                    ['name' => 'Pedro Porro', 'position' => 'DEF', 'nationality' => 'Spain', 'jersey_number' => 23],
                    ['name' => 'Cristian Romero', 'position' => 'DEF', 'nationality' => 'Argentina', 'jersey_number' => 17],
                    ['name' => 'Micky van de Ven', 'position' => 'DEF', 'nationality' => 'Netherlands', 'jersey_number' => 37],
                    ['name' => 'Destiny Udogie', 'position' => 'DEF', 'nationality' => 'Italy', 'jersey_number' => 38],
                    ['name' => 'Yves Bissouma', 'position' => 'MID', 'nationality' => 'Mali', 'jersey_number' => 8],
                    ['name' => 'James Maddison', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 10],
                    ['name' => 'Dejan Kulusevski', 'position' => 'MID', 'nationality' => 'Sweden', 'jersey_number' => 21],
                    ['name' => 'Son Heung-min', 'position' => 'FWD', 'nationality' => 'South Korea', 'jersey_number' => 7],
                    ['name' => 'Richarlison', 'position' => 'FWD', 'nationality' => 'Brazil', 'jersey_number' => 9],
                ]
            ],
            [
                'name' => 'West Ham United',
                'short_name' => 'WHU',
                'real_players' => [
                    ['name' => 'Alphonse Areola', 'position' => 'GK', 'nationality' => 'France', 'jersey_number' => 23],
                    ['name' => 'Vladimír Coufal', 'position' => 'DEF', 'nationality' => 'Czech Republic', 'jersey_number' => 5],
                    ['name' => 'Kurt Zouma', 'position' => 'DEF', 'nationality' => 'France', 'jersey_number' => 4],
                    ['name' => 'Nayef Aguerd', 'position' => 'DEF', 'nationality' => 'Morocco', 'jersey_number' => 27],
                    ['name' => 'Emerson Palmieri', 'position' => 'DEF', 'nationality' => 'Italy', 'jersey_number' => 33],
                    ['name' => 'Edson Álvarez', 'position' => 'MID', 'nationality' => 'Mexico', 'jersey_number' => 19],
                    ['name' => 'James Ward-Prowse', 'position' => 'MID', 'nationality' => 'England', 'jersey_number' => 7],
                    ['name' => 'Lucas Paquetá', 'position' => 'MID', 'nationality' => 'Brazil', 'jersey_number' => 10],
                    ['name' => 'Mohammed Kudus', 'position' => 'FWD', 'nationality' => 'Ghana', 'jersey_number' => 14],
                    ['name' => 'Jarrod Bowen', 'position' => 'FWD', 'nationality' => 'England', 'jersey_number' => 20],
                ]
            ],
            [
                'name' => 'Wolverhampton Wanderers',
                'short_name' => 'WOL',
                'real_players' => [
                    ['name' => 'José Sá', 'position' => 'GK', 'nationality' => 'Portugal', 'jersey_number' => 1],
                    ['name' => 'Nélson Semedo', 'position' => 'DEF', 'nationality' => 'Portugal', 'jersey_number' => 22],
                    ['name' => 'Max Kilman', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 23],
                    ['name' => 'Craig Dawson', 'position' => 'DEF', 'nationality' => 'England', 'jersey_number' => 15],
                    ['name' => 'Rayan Aït-Nouri', 'position' => 'DEF', 'nationality' => 'Algeria', 'jersey_number' => 3],
                    ['name' => 'Mario Lemina', 'position' => 'MID', 'nationality' => 'Gabon', 'jersey_number' => 5],
                    ['name' => 'João Gomes', 'position' => 'MID', 'nationality' => 'Brazil', 'jersey_number' => 8],
                    ['name' => 'Pablo Sarabia', 'position' => 'MID', 'nationality' => 'Spain', 'jersey_number' => 21],
                    ['name' => 'Pedro Neto', 'position' => 'FWD', 'nationality' => 'Portugal', 'jersey_number' => 7],
                    ['name' => 'Matheus Cunha', 'position' => 'FWD', 'nationality' => 'Brazil', 'jersey_number' => 12],
                ]
            ]
        ];

        $this->command->info('Creating Premier League teams and players...');

        $playerCounter = 202; // Start from PL-0202 to avoid conflicts with existing players
        foreach ($teams as $teamData) {
            // Create team
            $team = Team::create([
                'name' => $teamData['name'],
                'short_name' => $teamData['short_name'],
                'competition_id' => $competition->id,
                'club_id' => $club->id,
                'logo' => 'defaults/club-logo.png'
            ]);

            $this->command->info("Created team: {$team->name}");

            // Create real players for this team
            foreach ($teamData['real_players'] as $playerData) {
                $player = Player::create([
                    'name' => $playerData['name'],
                    'position' => $playerData['position'],
                    'nationality' => $playerData['nationality'],
                    'jersey_number' => $playerData['jersey_number'],
                    'team_id' => $team->id,
                    'club_id' => $club->id,
                    'date_of_birth' => fake()->dateTimeBetween('-35 years', '-18 years'),
                    'height' => fake()->numberBetween(165, 195),
                    'weight' => fake()->numberBetween(60, 85),
                    'preferred_foot' => fake()->randomElement(['left', 'right', 'both']),
                    'market_value' => fake()->numberBetween(1000000, 100000000),
                    'salary' => fake()->numberBetween(20000, 300000),
                    'contract_expiry' => fake()->dateTimeBetween('now', '+5 years'),
                    'fifa_connect_id' => 'PL-' . str_pad($playerCounter, 4, '0', STR_PAD_LEFT),
                    'status' => 'active'
                ]);

                $this->command->info("  - Created player: {$player->name} ({$player->position}) - ID: PL-" . str_pad($playerCounter, 4, '0', STR_PAD_LEFT));
                $playerCounter++;
            }

            // Generate additional players to reach 30 total
            $additionalPlayersNeeded = 30 - count($teamData['real_players']);
            for ($i = 0; $i < $additionalPlayersNeeded; $i++) {
                $positions = ['GK', 'DEF', 'MID', 'FWD'];
                $position = $positions[array_rand($positions)];
                
                $player = Player::create([
                    'name' => fake()->firstName() . ' ' . fake()->lastName(),
                    'position' => $position,
                    'nationality' => fake()->countryCode(),
                    'jersey_number' => $i + count($teamData['real_players']) + 1,
                    'team_id' => $team->id,
                    'club_id' => $club->id,
                    'date_of_birth' => fake()->dateTimeBetween('-35 years', '-16 years'),
                    'height' => fake()->numberBetween(165, 195),
                    'weight' => fake()->numberBetween(60, 85),
                    'preferred_foot' => fake()->randomElement(['left', 'right', 'both']),
                    'market_value' => fake()->numberBetween(100000, 50000000),
                    'salary' => fake()->numberBetween(50000, 200000),
                    'contract_expiry' => fake()->dateTimeBetween('now', '+5 years'),
                    'fifa_connect_id' => 'PL-' . str_pad($playerCounter, 4, '0', STR_PAD_LEFT),
                    'status' => 'active'
                ]);

                $this->command->info("  - Created player: {$player->name} ({$player->position}) - ID: PL-" . str_pad($playerCounter, 4, '0', STR_PAD_LEFT));
                $playerCounter++;
            }
        }

        $this->command->info('Premier League teams and players created successfully!');
        $this->command->info('Total teams created: ' . count($teams));
        $this->command->info('Total players created: ' . (count($teams) * 30));
    }
}
