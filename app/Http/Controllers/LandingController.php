<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Partner;
use App\Models\CarouselSlide;
use App\Models\NewsArticle;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the public landing page.
     */
     public function index(Request $request)
     {
         $categories = [
             'education' => ['label' => 'EDUCATION'],
             'health' => ['label' => 'HEALTH'],
             'governance' => ['label' => 'GOVERNANCE'],
             'active-citizenship' => ['label' => 'ACTIVE CITIZENSHIP'],
             'social-inclusion' => ['label' => 'SOCIAL INCLUSION'],
             'peace-building' => ['label' => 'PEACE BUILDING'],
             'environment' => ['label' => 'ENVIRONMENT'],
             'youth-employment' => ['label' => 'YOUTH EMPLOYMENT & EMPOWERMENT'],
             'agriculture' => ['label' => 'AGRICULTURE'],
             'global-mobility' => ['label' => 'GLOBAL MOBILITY'],
         ];
 
         $announcements = Announcement::active()
             ->latest()
             ->limit(3)
             ->get();
 
         $partners = Partner::where('is_active', true)->latest()->get();
         $slides = CarouselSlide::latest()->get();
 
         if ($slides->isEmpty()) {
             $formattedSlides = [
                 [
                     'title' => 'Empowering Namayan Youth Leaders',
                     'desc' => 'Access local government programs, health consultations, library slots, and tournament registrations easily.',
                     'image' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1200&q=80',
                     'cta1' => 'Book a Consultation',
                     'url1' => route('forms.health.create')
                 ],
                 [
                     'title' => 'Silid Karunungan Studying Spaces',
                     'desc' => 'Reserve a study space at our local modern library facilities with free high-speed internet and research tools.',
                     'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80',
                     'cta1' => 'Book Library Slot',
                     'url1' => route('forms.silid.create')
                 ],
                 [
                     'title' => 'Medicine Delivery Support Services',
                     'desc' => 'Apply digitally for the SK Pabili Medicine program to receive essential healthcare assistance directly to your home.',
                     'image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80',
                     'cta1' => 'Apply for Medicine',
                     'url1' => route('forms.medicine.create')
                 ],
                 [
                     'title' => 'SK Sports Leagues & Tournaments',
                     'desc' => 'Register teams or sign up individually for community basketball, volleyball, badminton, and esports leagues.',
                     'image' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&w=1200&q=80',
                     'cta1' => 'Register for Sports',
                     'url1' => route('forms.sports.create')
                 ]
             ];
         } else {
             $formattedSlides = $slides->map(function($slide) {
                 return [
                     'title' => $slide->title,
                     'desc' => $slide->description,
                     'image' => asset('storage/' . $slide->image_path),
                     'cta1' => $slide->cta_text ?? 'Apply Now',
                     'url1' => $slide->cta_url ?? '#'
                 ];
             })->all();
         }

        // Load news articles for landing page (Featured & Recent only)
        $dbArticles = NewsArticle::latest()->get();

        if ($dbArticles->isEmpty()) {
            // Mock Fallbacks matching the user's design image
            $featuredArticle = (object)[
                'title' => 'Record-Breaking, Stunning Performance Leads The Swimmer To A Victory, Securing Their Place In History.',
                'slug' => 'record-breaking-swimmer-victory',
                'category' => 'Swimming',
                'read_time' => 10,
                'excerpt' => 'A spectacular final round performance secure the gold medal and set a new standard for local athletic accomplishments in the community games.',
                'content' => "Record-breaking, stunning performance leads the swimmer to a victory, securing their place in history. Local athletes competed at the annual community tournament, showing incredible speed and determination. Coaches and spectators celebrated this milestone event, which marks a new chapter in the local sports calendar.",
                'image_path' => 'https://images.unsplash.com/photo-1519766304817-4f37bda74a27?auto=format&fit=crop&w=1200&q=80',
                'published_at' => now(),
            ];

            $recentArticles = collect([
                (object)[
                    'title' => 'A Quiet Moment in the Crowd: A Monk Dives Into the News',
                    'slug' => 'quiet-moment-crowd-monk-news',
                    'category' => 'News',
                    'read_time' => 6,
                    'excerpt' => 'Amidst the hustle and bustle of a busy street, a monk quietly reads a newspaper, seemingly absorbed in the world...',
                    'content' => "Amidst the hustle and bustle of a busy street, a monk quietly reads a newspaper, seemingly absorbed in the world. Spectators described the peaceful scene as a reminder of mindfulness amidst urban noise.",
                    'image_path' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80',
                    'published_at' => now(),
                ],
                (object)[
                    'title' => 'Romania and Bulgaria fully join Europe\'s borderless travel zone',
                    'slug' => 'romania-bulgaria-borderless-travel',
                    'category' => 'Travel',
                    'read_time' => 10,
                    'excerpt' => 'Amidst the hustle and bustle of a busy street, a monk quietly reads a newspaper, seemingly absorbed in the world...',
                    'content' => "Romania and Bulgaria fully join Europe's borderless travel zone. Both nations have officially completed their integrations, opening up seamless transportation and economic pathways.",
                    'image_path' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=600&q=80',
                    'published_at' => now(),
                ],
                (object)[
                    'title' => 'Emerging Startups Showcase Innovations at Local Tech',
                    'slug' => 'emerging-startups-innovations-local-tech',
                    'category' => 'Technology',
                    'read_time' => 12,
                    'excerpt' => 'Amidst the hustle and bustle of a busy street, a monk quietly reads a newspaper, seemingly absorbed in the world...',
                    'content' => "Emerging startups showcase innovations at local tech hubs. A display of newly developed systems and platforms attracted local tech enthusiasts and potential capital investors.",
                    'image_path' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=600&q=80',
                    'published_at' => now(),
                ]
            ]);
        } else {
            // Query from Database
            $featuredArticle = NewsArticle::where('is_featured', true)->latest()->first();
            if (!$featuredArticle) {
                $featuredArticle = NewsArticle::latest()->first();
            }

            $featuredId = optional($featuredArticle)->id;

            $recentArticles = NewsArticle::when($featuredId, function($q) use ($featuredId) {
                    return $q->where('id', '!=', $featuredId);
                })
                ->latest()
                ->limit(3)
                ->get();
        }

        $initiatives = \App\Models\Initiative::whereNotNull('form_route')->get()->keyBy('form_route');

        return view('landing.index', compact(
            'categories', 
            'announcements', 
            'partners', 
            'formattedSlides',
            'featuredArticle',
            'recentArticles',
            'initiatives'
        ));
    }

    /**
     * Display the public news listing page.
     */
    public function newsIndex(Request $request)
    {
        $dbArticles = NewsArticle::latest()->get();

        if ($dbArticles->isEmpty()) {
            // Mock Fallbacks matching the user's design image
            $trendingArticles = collect([
                (object)[
                    'title' => 'Innovative Farming Technology Transforms Local Agriculture Practices',
                    'slug' => 'farming-tech-transforms-agriculture',
                    'category' => 'Agriculture',
                    'read_time' => 24,
                    'excerpt' => 'Innovative technology is changing local farming methods. Farmers can now increase efficiency and sustainability...',
                    'content' => "Innovative technology is changing local farming methods. Farmers can now increase efficiency and sustainability through automated sensor networks.",
                    'image_path' => 'https://images.unsplash.com/photo-1586771107445-d3ca888129ff?auto=format&fit=crop&w=600&q=80',
                    'published_at' => now(),
                ],
                (object)[
                    'title' => 'Cultural Festival Highlights Diversity Through Food and Performances',
                    'slug' => 'cultural-festival-highlights-diversity',
                    'category' => 'Culture',
                    'read_time' => 17,
                    'excerpt' => 'A vibrant festival will celebrate diversity with food and performances. Local cultures will be showcased throughout the event...',
                    'content' => "A vibrant festival will celebrate diversity with food and performances. Local cultures will be showcased throughout the event, attracting tourists and fostering strong community ties.",
                    'image_path' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&w=600&q=80',
                    'published_at' => now(),
                ],
                (object)[
                    'title' => 'Regional Sports League Welcomes New Teams and Opportunities',
                    'slug' => 'sports-league-new-teams',
                    'category' => 'Sports',
                    'read_time' => 19,
                    'excerpt' => 'This initiative aims to promote sports participation and community engagement, providing a platform for athletes to showcase...',
                    'content' => "This initiative aims to promote sports participation and community engagement, providing a platform for athletes to showcase their talent on a regional stage.",
                    'image_path' => 'https://images.unsplash.com/photo-1517649763962-0c623066013b?auto=format&fit=crop&w=600&q=80',
                    'published_at' => now(),
                ]
            ]);
        } else {
            $trendingArticles = NewsArticle::where('is_trending', true)
                ->latest()
                ->limit(3)
                ->get();
        }

        return view('news.index', compact('trendingArticles'));
    }

    /**
     * Display the detailed news article view.
     */
    public function showNews(Request $request, $slug)
    {
        $article = NewsArticle::where('slug', $slug)->first();

        if (!$article) {
            // Mock Fallback matching fallback slug to support preview/navigation
            $mockArticles = [
                'record-breaking-swimmer-victory' => [
                    'title' => 'Record-Breaking, Stunning Performance Leads The Swimmer To A Victory, Securing Their Place In History.',
                    'category' => 'Swimming',
                    'read_time' => 10,
                    'excerpt' => 'A spectacular final round performance secure the gold medal and set a new standard for local athletic accomplishments in the community games.',
                    'content' => "Record-breaking, stunning performance leads the swimmer to a victory, securing their place in history. Local athletes competed at the annual community tournament, showing incredible speed and determination. Coaches and spectators celebrated this milestone event, which marks a new chapter in the local sports calendar.",
                    'image_path' => 'https://images.unsplash.com/photo-1519766304817-4f37bda74a27?auto=format&fit=crop&w=1200&q=80',
                    'published_at' => now(),
                ],
                'quiet-moment-crowd-monk-news' => [
                    'title' => 'A Quiet Moment in the Crowd: A Monk Dives Into the News',
                    'category' => 'News',
                    'read_time' => 6,
                    'excerpt' => 'Amidst the hustle and bustle of a busy street, a monk quietly reads a newspaper, seemingly absorbed in the world...',
                    'content' => "Amidst the hustle and bustle of a busy street, a monk quietly reads a newspaper, seemingly absorbed in the world. Spectators described the peaceful scene as a reminder of mindfulness amidst urban noise.",
                    'image_path' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80',
                    'published_at' => now(),
                ],
                'romania-bulgaria-borderless-travel' => [
                    'title' => 'Romania and Bulgaria fully join Europe\'s borderless travel zone',
                    'category' => 'Travel',
                    'read_time' => 10,
                    'excerpt' => 'Amidst the hustle and bustle of a busy street, a monk quietly reads a newspaper, seemingly absorbed in the world...',
                    'content' => "Romania and Bulgaria fully join Europe's borderless travel zone. Both nations have officially completed their integrations, opening up seamless transportation and economic pathways.",
                    'image_path' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=600&q=80',
                    'published_at' => now(),
                ],
                'emerging-startups-innovations-local-tech' => [
                    'title' => 'Emerging Startups Showcase Innovations at Local Tech',
                    'category' => 'Technology',
                    'read_time' => 12,
                    'excerpt' => 'Amidst the hustle and bustle of a busy street, a monk quietly reads a newspaper, seemingly absorbed in the world...',
                    'content' => "Emerging startups showcase innovations at local tech hubs. A display of newly developed systems and platforms attracted local tech enthusiasts and potential capital investors.",
                    'image_path' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=600&q=80',
                    'published_at' => now(),
                ],
                'farming-tech-transforms-agriculture' => [
                    'title' => 'Innovative Farming Technology Transforms Local Agriculture Practices',
                    'category' => 'Agriculture',
                    'read_time' => 24,
                    'excerpt' => 'Innovative technology is changing local farming methods. Farmers can now increase efficiency and sustainability...',
                    'content' => "Innovative technology is changing local farming methods. Farmers can now increase efficiency and sustainability through automated sensor networks.",
                    'image_path' => 'https://images.unsplash.com/photo-1586771107445-d3ca888129ff?auto=format&fit=crop&w=600&q=80',
                    'published_at' => now(),
                ],
                'cultural-festival-highlights-diversity' => [
                    'title' => 'Cultural Festival Highlights Diversity Through Food and Performances',
                    'category' => 'Culture',
                    'read_time' => 17,
                    'excerpt' => 'A vibrant festival will celebrate diversity with food and performances. Local cultures will be showcased throughout the event...',
                    'content' => "A vibrant festival will celebrate diversity with food and performances. Local cultures will be showcased throughout the event, attracting tourists and fostering strong community ties.",
                    'image_path' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&w=600&q=80',
                    'published_at' => now(),
                ],
                'sports-league-new-teams' => [
                    'title' => 'Regional Sports League Welcomes New Teams and Opportunities',
                    'category' => 'Sports',
                    'read_time' => 19,
                    'excerpt' => 'This initiative aims to promote sports participation and community engagement, providing a platform for athletes to showcase...',
                    'content' => "This initiative aims to promote sports participation and community engagement, providing a platform for athletes to showcase their talent on a regional stage.",
                    'image_path' => 'https://images.unsplash.com/photo-1517649763962-0c623066013b?auto=format&fit=crop&w=600&q=80',
                    'published_at' => now(),
                ]
            ];
 
            if (isset($mockArticles[$slug])) {
                $article = (object)$mockArticles[$slug];
            } else {
                abort(404);
            }
        }

        return view('news.show', compact('article'));
    }
}
