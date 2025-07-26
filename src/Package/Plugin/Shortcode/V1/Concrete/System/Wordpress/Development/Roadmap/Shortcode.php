<?php

namespace Ababilithub\FlexAuthorization\Package\Plugin\Shortcode\V1\Concrete\System\Wordpress\Development\Roadmap;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexWordpress\Package\Shortcode\V1\Base\Shortcode as BaseShortcode,
};

use const Ababilithub\{
    FlexAuthorization\PLUGIN_PRE_UNDS,
    FlexAuthorization\PLUGIN_PRE_HYPH,
    FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Land\Deed\POSTTYPE
};

class Shortcode extends BaseShortcode
{
    public function init(): void
    {
        $this->set_tag('ababilithub-wordpress-development-roadmap'); 

        $this->set_default_attributes([
            'style' => 'grid',
            'columns' => '3',
            'pagination' => 'yes',
            'show' => '10',
            'sort' => 'DESC',
            'sort_by' => 'date',
            'status' => 'publish',
            'pagination_style' => 'load_more',
            'search_filter' => 'yes',
            'sidebar_filter' => 'yes',
            'deed_type' => '',
            'district' => '',
            'thana' => '',
            'debug' => 'no'
        ]);

        $this->init_hook();
        $this->init_service();
    }

    public function init_hook(): void
    {
        add_action(PLUGIN_PRE_UNDS.'_wordpress_development_roadmap', [$this, 'wordpress_development_roadmap']);
    }

    public function init_service(): void
    {
        //new PosttypeListTemplate();
    }

    public function render(array $attributes): string
    {
        $this->set_attributes($attributes);
        $params = $this->get_attributes();
        
        ob_start();
        do_action(PLUGIN_PRE_UNDS.'_wordpress_development_roadmap', $params);
        return ob_get_clean();
    }

    public function wordpress_development_roadmap(array $params): void
    {
        try {
            // Output the roadmap HTML structure
            echo '
            <div class="flex-roadmap-container">
                <div class="flex-roadmap-header">
                    <h2 class="flex-roadmap-title">' . esc_html__('WordPress Development Roadmap', 'flex-authorization') . '</h2>
                    <p class="flex-roadmap-description">' . esc_html__('A comprehensive guide to mastering WordPress plugin development with a freemium model', 'flex-authorization') . '</p>
                </div>
                
                <div class="flex-roadmap-phases">';
            
            // Define roadmap phases
            $phases = [
                [
                    'title' => __('WordPress Development Fundamentals ( 10% )', 'flex-authorization'),
                    'progress' => 10,
                    'description' => __('Understand the WordPress core, theme/plugin structure, and hooks.', 'flex-authorization'),
                    'tasks' => [
                        __('Study the WordPress file structure', 'flex-authorization'),
                        __('Learn WordPress hooks (actions & filters)', 'flex-authorization'),
                        __('Follow WordPress coding standards', 'flex-authorization')
                    ]
                ],
                [
                    'title' => __('Custom Plugin Development ( 20% )', 'flex-authorization'),
                    'progress' => 30,
                    'description' => __('Learn to create and structure a WordPress plugin professionally.', 'flex-authorization'),
                    'tasks' => [
                        __('Set up a plugin folder with the correct structure', 'flex-authorization'),
                        __('Register shortcodes, widgets, and custom post types', 'flex-authorization'),
                        __('Create a "Custom Post Type Manager" plugin', 'flex-authorization')
                    ]
                ],
                [
                    'title' => __('Database Management & Optimization  ( 10% )', 'flex-authorization'),
                    'progress' => 40,
                    'description' => __('Learn how to interact with the WordPress database safely.', 'flex-authorization'),
                    'tasks' => [
                        __('Understand the WordPress database structure', 'flex-authorization'),
                        __('Use caching strategies', 'flex-authorization'),
                        __('Build a custom report generator plugin', 'flex-authorization')
                    ]
                ],
                [
                    'title' => __('Security & Best Practices ( 10% )', 'flex-authorization'),
                    'progress' => 50,
                    'description' => __('Secure WordPress plugins against common vulnerabilities.', 'flex-authorization'),
                    'tasks' => [
                        __('Learn nonce verification, data sanitization, and escaping', 'flex-authorization'),
                        __('Prevent SQL injection, XSS, and CSRF attacks', 'flex-authorization'),
                        __('Implement nonce verification for a custom API endpoint', 'flex-authorization')
                    ]
                ],
                [
                    'title' => __('API Development & Integration ( 15% )', 'flex-authorization'),
                    'progress' => 65,
                    'description' => __('Work with REST APIs and build custom API endpoints.', 'flex-authorization'),
                    'tasks' => [
                        __('Learn REST API fundamentals', 'flex-authorization'),
                        __('Implement authentication and security for API requests', 'flex-authorization'),
                        __('Build a "Custom API for Posts" plugin', 'flex-authorization')
                    ]
                ],
                [
                    'title' => __('Node.js for WordPress Plugin Enhancement ( 15% )', 'flex-authorization'),
                    'progress' => 80,
                    'description' => __('Use Node.js to build interactive, real-time WordPress features.', 'flex-authorization'),
                    'tasks' => [
                        __('Learn Node.js and asynchronous JavaScript', 'flex-authorization'),
                        __('Use Express.js to create a custom API', 'flex-authorization'),
                        __('Create a real-time notification system for WordPress', 'flex-authorization')
                    ]
                ],
                [
                    'title' => __('Monetization & Freemium Model ( 10% )', 'flex-authorization'),
                    'progress' => 90,
                    'description' => __('Implement a freemium business model for WordPress plugins.', 'flex-authorization'),
                    'tasks' => [
                        __('Implement license key validation for premium features', 'flex-authorization'),
                        __('Explore Freemius, EDD, and WooCommerce for selling plugins', 'flex-authorization'),
                        __('Convert a free plugin into a freemium plugin', 'flex-authorization')
                    ]
                ],
                [
                    'title' => __('Deployment, Testing & Maintenance ( 10% )', 'flex-authorization'),
                    'progress' => 100,
                    'description' => __('Automate plugin deployment and improve testing.', 'flex-authorization'),
                    'tasks' => [
                        __('Use Composer for dependency management', 'flex-authorization'),
                        __('Automate plugin deployment to WordPress.org', 'flex-authorization'),
                        __('Set up GitHub Actions for automated plugin testing', 'flex-authorization')
                    ]
                ]
            ];
            
            // Output each phase
            foreach ($phases as $index => $phase) {
                echo '
                <div class="flex-roadmap-phase">
                    <div class="flex-phase-header" onclick="flexTogglePhaseDetails(' . $index . ')">
                        <div class="flex-phase-number">' . ($index + 1) . '</div>
                        <div class="flex-phase-content">
                            <h3 class="flex-phase-title">' . esc_html($phase['title']) . '</h3>
                            <div class="flex-phase-progress">
                                <div class="flex-progress-bar" style="width: ' . $phase['progress'] . '%;"></div>
                                <span class="flex-progress-text">' . $phase['progress'] . '%</span>
                            </div>
                        </div>
                        <div class="flex-phase-toggle">
                            <span class="flex-toggle-icon">+</span>
                        </div>
                    </div>
                    <div class="flex-phase-details" id="flex-phase-details-' . $index . '">
                        <p class="flex-phase-description">' . esc_html($phase['description']) . '</p>
                        <ul class="flex-phase-tasks">';
                
                foreach ($phase['tasks'] as $task) {
                    echo '<li>' . esc_html($task) . '</li>';
                }
                
                echo '
                        </ul>
                    </div>
                </div>';
            }
            
            echo '
                </div>
            </div>
            
            <style>
                .flex-roadmap-container {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;
                    max-width: 900px;
                    margin: 0 auto;
                    padding: 20px;
                    background: #fff;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                }
                
                .flex-roadmap-header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 1px solid #eee;
                }
                
                .flex-roadmap-title {
                    color: #1d2327;
                    margin: 0 0 10px;
                    font-size: 28px;
                }
                
                .flex-roadmap-description {
                    color: #646970;
                    margin: 0;
                    font-size: 16px;
                }
                
                .flex-roadmap-phases {
                    display: flex;
                    flex-direction: column;
                    gap: 15px;
                }
                
                .flex-roadmap-phase {
                    border: 1px solid #dcdcde;
                    border-radius: 4px;
                    overflow: hidden;
                    transition: all 0.3s ease;
                }
                
                .flex-phase-header {
                    display: flex;
                    align-items: center;
                    padding: 15px 20px;
                    background: #f6f7f7;
                    cursor: pointer;
                    transition: background 0.3s ease;
                }
                
                .flex-phase-header:hover {
                    background: #f0f0f1;
                }
                
                .flex-phase-number {
                    width: 30px;
                    height: 30px;
                    background: #2271b1;
                    color: white;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    margin-right: 15px;
                    flex-shrink: 0;
                }
                
                .flex-phase-content {
                    flex-grow: 1;
                }
                
                .flex-phase-title {
                    margin: 0 0 8px;
                    color: #1d2327;
                    font-size: 18px;
                }
                
                .flex-phase-progress {
                    height: 8px;
                    background: #dcdcde;
                    border-radius: 4px;
                    position: relative;
                    overflow: hidden;
                }
                
                .flex-progress-bar {
                    height: 100%;
                    background: #2271b1;
                    border-radius: 4px;
                    transition: width 0.5s ease;
                }
                
                .flex-progress-text {
                    position: absolute;
                    right: 5px;
                    top: -20px;
                    font-size: 12px;
                    color: #646970;
                }
                
                .flex-phase-toggle {
                    margin-left: 15px;
                }
                
                .flex-toggle-icon {
                    font-size: 20px;
                    color: #646970;
                    transition: transform 0.3s ease;
                }
                
                .flex-phase-details {
                    padding: 0;
                    max-height: 0;
                    overflow: hidden;
                    transition: max-height 0.3s ease, padding 0.3s ease;
                    background: white;
                }
                
                .flex-phase-details.active {
                    padding: 20px;
                    max-height: 500px;
                }
                
                .flex-phase-description {
                    margin: 0 0 15px;
                    color: #3c434a;
                    font-size: 15px;
                    line-height: 1.5;
                }
                
                .flex-phase-tasks {
                    margin: 0;
                    padding-left: 20px;
                    color: #3c434a;
                }
                
                .flex-phase-tasks li {
                    margin-bottom: 8px;
                    line-height: 1.4;
                }
                
                @media (max-width: 600px) {
                    .flex-phase-header {
                        flex-wrap: wrap;
                    }
                    
                    .flex-phase-number {
                        margin-bottom: 10px;
                    }
                    
                    .flex-phase-content {
                        width: 100%;
                    }
                }
            </style>
            
            <script>
                function flexTogglePhaseDetails(index) {
                    const details = document.getElementById("flex-phase-details-" + index);
                    const icon = document.querySelector(`#flex-phase-details-${index}`).previousElementSibling.querySelector(".flex-toggle-icon");
                    
                    details.classList.toggle("active");
                    icon.textContent = details.classList.contains("active") ? "−" : "+";
                }
            </script>';
            
        } catch (Exception $e) {
            if ($params['debug'] === 'yes') {
                echo '<div class="flex-roadmap-error">' . esc_html__('Error: ', 'flex-authorization') . esc_html($e->getMessage()) . '</div>';
            } else {
                echo '<div class="flex-roadmap-error">' . esc_html__('Unable to display the roadmap at this time.', 'flex-authorization') . '</div>';
            }
        }
    }
}