<?php

namespace Database\Seeders\Homepage;

use App\Models\Homepage\Homepage;
use Illuminate\Database\Seeder;

class HomepageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Homepage::truncate();

        $contents = array(
            [
                'value' => [
                    'SECTION1' => [
                        "content" => "<h1>Your Nusa Lembongan<br/>Story Starts Here</h1>",
                        "backgroundVideo" => "PLACEHOLDER_hero_banner.mp4",
                        "isVideo" => true,
                        "videoThumbnail" => "PLACEHOLDER_hero_banner_thumbnail.jpg",
                        "searchLabelDates" => "Dates",
                        "searchPlaceholderDates" => "Choose Date",
                        "searchLabelGuest" => "Guest",
                        "searchPlaceholderGuest" => "2 Adults, 0 Children",
                        "searchLabelCollection" => "Collections",
                        "searchPlaceholderCollection" => "Villas / Resort",
                        "buttonText" => "Search",
                        "buttonLink" => "#",
                    ],
                    'SECTION2' => [
                        "content" => "<h2>Nusa Lembongan, Your Barefoot Escape From The Ordinary</h2>\r\n<p>Ringed by crystal clear turquoise waters and just 30 minutes from Bali. Wake to the sound of waves, the warmth of tropical sun on your skin, and the distant hum of a scooter drifting past from a nearby village, a gentle reminder that real-life here beats quirky alongside this rhythm.</p>",
                        "image" => "PLACEHOLDER_about_image.jpg",
                        "mapImage" => "PLACEHOLDER_bali_lembongan_map.jpg",
                        "sideContent" => "<p>The Lembongan Traveller is your trusted, Australian-owned complete guide to Nusa Lembongan and Nusa Ceningan. More than a place to rest your head, our handpicked collection of villas, resorts, and bungalows puts you right at the heart of island life. We make it easy to find your perfect stay.</p>",
                        "buttonText" => "Discover More",
                        "buttonLink" => "#",
                    ],
                    'SECTION3' => [
                        "content" => "<h2 style=\"text-align: center;\">Find Your Rhythm, Live The Island Life</h2>",
                        "tabs" => [
                            [
                                "tabName" => "Things To Do",
                                "items" => [
                                    [
                                        'image' => 'PLACEHOLDER_surfing.jpg',
                                        'title' => 'World Class Surfing Spot',
                                        'description' => 'Ride the barrels of Shipwreck, Lacerations and Playgrounds, some of Bali\'s most iconic waves for all levels.',
                                        'buttonText' => 'Explore',
                                        'buttonLink' => '#',
                                    ],
                                    [
                                        'image' => 'PLACEHOLDER_manta.jpg',
                                        'title' => 'Manta Point Snorkeling',
                                        'description' => 'Swim alongside gentle giant manta rays in their natural cleaning station just off Nusa Penida.',
                                        'buttonText' => 'Explore',
                                        'buttonLink' => '#',
                                    ],
                                    [
                                        'image' => 'PLACEHOLDER_mangrove.jpg',
                                        'title' => 'Mangrove Tour',
                                        'description' => 'Cruise through the calm mangrove forest by traditional boat and discover the island\'s hidden ecosystem.',
                                        'buttonText' => 'Explore',
                                        'buttonLink' => '#',
                                    ],
                                    [
                                        'image' => 'PLACEHOLDER_jetski.jpg',
                                        'title' => 'Jetski Through Coral Cave',
                                        'description' => 'Explore hidden coves and coral caves around the island with a guided jetski adventure.',
                                        'buttonText' => 'Explore',
                                        'buttonLink' => '#',
                                    ],
                                ],
                            ],
                            [
                                "tabName" => "Island Travel Tips",
                                "items" => [
                                    [
                                        'image' => 'PLACEHOLDER_tips1.jpg',
                                        'title' => 'Best Time To Visit',
                                        'description' => 'Placeholder text, edit with the ideal season and weather information for visiting the island.',
                                        'buttonText' => 'Explore',
                                        'buttonLink' => '#',
                                    ],
                                    [
                                        'image' => 'PLACEHOLDER_tips2.jpg',
                                        'title' => 'Getting Around',
                                        'description' => 'Placeholder text, edit with transportation tips such as scooter rental and boat schedules.',
                                        'buttonText' => 'Explore',
                                        'buttonLink' => '#',
                                    ],
                                    [
                                        'image' => 'PLACEHOLDER_tips3.jpg',
                                        'title' => 'What To Pack',
                                        'description' => 'Placeholder text, edit with packing recommendations for the island.',
                                        'buttonText' => 'Explore',
                                        'buttonLink' => '#',
                                    ],
                                    [
                                        'image' => 'PLACEHOLDER_tips4.jpg',
                                        'title' => 'Local Etiquette',
                                        'description' => 'Placeholder text, edit with cultural tips for visitors.',
                                        'buttonText' => 'Explore',
                                        'buttonLink' => '#',
                                    ],
                                ],
                            ],
                            [
                                "tabName" => "Accommodation",
                                "items" => [
                                    [
                                        'image' => 'PLACEHOLDER_accom1.jpg',
                                        'title' => 'Beachfront Villas',
                                        'description' => 'Placeholder text, edit with description of beachfront villa options.',
                                        'buttonText' => 'Explore',
                                        'buttonLink' => '#',
                                    ],
                                    [
                                        'image' => 'PLACEHOLDER_accom2.jpg',
                                        'title' => 'Boutique Resorts',
                                        'description' => 'Placeholder text, edit with description of boutique resort options.',
                                        'buttonText' => 'Explore',
                                        'buttonLink' => '#',
                                    ],
                                    [
                                        'image' => 'PLACEHOLDER_accom3.jpg',
                                        'title' => 'Budget Homestays',
                                        'description' => 'Placeholder text, edit with description of homestay options.',
                                        'buttonText' => 'Explore',
                                        'buttonLink' => '#',
                                    ],
                                    [
                                        'image' => 'PLACEHOLDER_accom4.jpg',
                                        'title' => 'Luxury Retreats',
                                        'description' => 'Placeholder text, edit with description of luxury retreat options.',
                                        'buttonText' => 'Explore',
                                        'buttonLink' => '#',
                                    ],
                                ],
                            ],
                        ],
                    ],

                    'SECTION4' => [
                        "content" => "<h2 style=\"text-align: center;\">Explore Lembongan</h2>",
                        "items" => [
                            [
                                'image' => 'PLACEHOLDER_mushroom_bay.jpg',
                                'title' => 'Mushroom & Sandy Bay',
                                'description' => 'Placeholder text, edit with a short description of Mushroom & Sandy Bay.',
                                'buttonText' => 'Explore',
                                'buttonLink' => '#',
                            ],
                            [
                                'image' => 'PLACEHOLDER_jungutbatu.jpg',
                                'title' => 'Jungutbatu',
                                'description' => 'Placeholder text, edit with a short description of Jungutbatu village and beach.',
                                'buttonText' => 'Explore',
                                'buttonLink' => '#',
                            ],
                            [
                                'image' => 'PLACEHOLDER_nusa_ceningan.jpg',
                                'title' => 'Nusa Ceningan',
                                'description' => 'Placeholder text, edit with a short description of Nusa Ceningan.',
                                'buttonText' => 'Explore',
                                'buttonLink' => '#',
                            ],
                        ],
                    ],
                    'SECTION5' => [
                        "label" => "Limited Offer",
                        "content" => "<h2>Book In Advance</h2>\r\n<p>Reserve ahead of time and save on your stay in Nusa Lembongan.</p>",
                        "background" => "PLACEHOLDER_book_in_advance_bg.jpg",
                        "buttonText" => "Book Now",
                        "buttonLink" => "#",
                    ],
                    'SECTION6' => [
                        "content" => "<h2>Exclusive Stay</h2>\r\n<p>Ringed by crystal clear turquoise waters and just 30 minutes from Bali. Wake to the sound of waves, the warmth of tropical sun on your skin, and the distant hum of a scooter drifting past from a nearby village.</p>",
                        "buttonText" => "All Properties",
                        "buttonLink" => "#",
                    ],
                    'SECTION7' => [
                        "content" => "<h2 style=\"text-align: center; color:white;\">Why Book With Us?</h2>",
                        "items" => [
                            [
                                'icon' => 'PLACEHOLDER_icon_experience.svg',
                                'title' => 'Full Purchase Experience',
                                'description' => 'Placeholder text, edit with details about the booking experience.',
                            ],
                            [
                                'icon' => 'PLACEHOLDER_icon_price.svg',
                                'title' => 'Price Match All Online Agents',
                                'description' => 'Placeholder text, edit with details about the price match guarantee.',
                            ],
                            [
                                'icon' => 'PLACEHOLDER_icon_budget.svg',
                                'title' => 'Accommodation To Suit All Budgets',
                                'description' => 'Placeholder text, edit with details about accommodation options.',
                            ],
                            [
                                'icon' => 'PLACEHOLDER_icon_office.svg',
                                'title' => 'Lembongan Office Open 7 Days',
                                'description' => 'Placeholder text, edit with office hours and contact details.',
                            ],
                        ],
                    ],

                    'SECTION8' => [
                        "content" => "<h2 style=\"text-align: center;\">Media Coverage</h2>",
                        "buttonText" => "View More",
                        "buttonLink" => "#",
                    ],
                    'SECTION9' => [
                        "logo" => "PLACEHOLDER_love_lembongan_logo.png",
                        "content" => "<h2 style=\"color:white;\">Our Dedication From Island, For The Island</h2>\r\n<p style=\"color:white;\">Placeholder text, edit with a description of the community initiative and how bookings help support the local island community and healthcare.</p>",
                        "buttonText" => "Volunteer For Lembongan",
                        "buttonLink" => "#",
                        "backgroundImage" => "PLACEHOLDER_dedication_bg.jpg",
                        "images" => [
                            ['image' => 'PLACEHOLDER_dedication1.jpg'],
                            ['image' => 'PLACEHOLDER_dedication2.jpg'],
                        ],
                    ],

                    'SECTION10' => [
                        "content" => "<h2>Keep Up With Us</h2>",
                        "buttonText" => "View More",
                        "buttonLink" => "#",
                    ],

                    'SECTION11' => [
                        "content" => "<h2>Getting To Lembongan</h2>\r\n<p>Placeholder text, edit with directions on how to get to Lembongan (fast boat, harbour, schedule, etc).</p>",
                        "address" => "Placeholder address, edit with the office address.",
                        "phone" => "+62 000 0000 0000",
                        "gmapsEmbed" => "https://www.google.com/maps/embed?pb=PLACEHOLDER_EMBED_CODE",
                    ],
                    'SECTION12' => [
                        "content" => "<h2 style=\"text-align: center;\">Frequently Asked Questions</h2>",
                    ],

                    'SECTION13' => [
                        "label" => "Newsletter",
                        "content" => "<h2 style=\"text-align: center; color:white;\">Stay In The Know</h2>\r\n<p style=\"text-align: center; color:white;\">Placeholder text, edit with a short invitation to subscribe to the newsletter.</p>",
                        "background" => "PLACEHOLDER_newsletter_bg.jpg",
                        "inputPlaceholder" => "Enter your email",
                        "buttonText" => "Subscribe",
                    ],
                ],
                'locale' => 'en',
            ],
        );

        foreach ($contents as $content) {
            Homepage::create([
                'value' => $content['value'],
                'locale' => $content['locale'],
            ]);
        }
    }
}