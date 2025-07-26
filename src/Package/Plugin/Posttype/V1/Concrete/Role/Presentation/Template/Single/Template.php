<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Role\Presentation\Template\Single;

(defined('ABSPATH') && defined('WPINC')) || die();

use Ababilithub\{
    FlexPhp\Package\Mixin\V1\Standard\Mixin as StandardMixin,
    FlexWordpress\Package\Mixin\V1\Standard\Mixin as StandardWpMixin,
    FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Role\Posttype as RolePosttype,
};

use const Ababilithub\{
    FlexMasterPro\PLUGIN_NAME,
    FlexMasterPro\PLUGIN_DIR,
    FlexMasterPro\PLUGIN_URL,
    FlexMasterPro\PLUGIN_FILE,
    FlexMasterPro\PLUGIN_PRE_UNDS,
    FlexMasterPro\PLUGIN_PRE_HYPH,
    FlexMasterPro\PLUGIN_VERSION,
};

class Template 
{
    use StandardMixin, StandardWpMixin;

    private $package;
    private $template_url;
    private $asset_url;
    private $posttype;

    public function __construct() 
    {
        $this->posttype = RolePosttype::POSTTYPE;
        $this->asset_url = $this->get_url('Asset/');
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts' ) );
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts' ) );
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script('jquery');

        wp_enqueue_style(
            PLUGIN_PRE_HYPH.'-'.$this->posttype.'-template-style', 
            $this->asset_url.'Css/Style.css',
            array(), 
            time()
        );

        wp_enqueue_script(
            PLUGIN_PRE_HYPH.'-'.$this->posttype.'-template-script', 
            $this->asset_url.'Js/Script.js',
            array(), 
            time(), 
            true
        );
        
        wp_localize_script(
            PLUGIN_PRE_HYPH.'-'.$this->posttype.'-template-localize-script', 
            PLUGIN_PRE_UNDS.'_'.$this->posttype.'_template_localize', 
            array(
                'adminAjaxUrl' => admin_url('admin-ajax.php'),
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'ajaxNonce' => wp_create_nonce(PLUGIN_PRE_UNDS.'_'.$this->posttype.'_nonce'),
                // 'ajaxAction' => PLUGIN_PRE_UNDS . '_action',
                // 'ajaxData' => PLUGIN_PRE_UNDS . '_data',
                // 'ajaxError' => PLUGIN_PRE_UNDS . '_error',
            )
        );
    }

    public static function single_post($post = null)
    {
        // Use passed post or fall back to global
        $post = $post ?: get_post();
        
        if (!$post) {
            return '';
        }
        
        // Setup post data
        setup_postdata($post);
        
        ob_start();
        ?>
        <main class="post-containr">
            <article class="post-article" id="post-<?php echo $post->ID; ?>">
                <!-- Hero Section -->
                <header class="post-hero">
                    <?php if (has_post_thumbnail($post->ID)) : ?>
                        <div class="post-featured-image">
                            <?php echo get_the_post_thumbnail($post->ID, 'large', ['class' => 'hero-image']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="hero-overlay"></div>
                    
                    <div class="hero-content">
                        <div class="container">
                            <h1 class="post-title"><?php echo get_the_title($post); ?></h1>
                            <div class="post-meta">
                                <span class="meta-item">
                                    <i class="fas fa-calendar"></i> <?php echo get_the_date('', $post); ?>
                                </span>
                                <?php if ($post_number = get_post_meta($post->ID, 'post-number', true)) : ?>
                                    <span class="meta-item">
                                        <i class="fas fa-file-alt"></i> <?php echo esc_html($post_number); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main Content Grid -->
                <div class="post-grid container">
                    <!-- Primary Content Column -->
                    <div class="post-main-content">
                        
                        <!-- Location Information -->
                        <section class="post-section location-info">
                            <h2 class="section-title">
                                <i class="fas fa-map-marked-alt"></i>
                                <?php esc_html_e('Location', 'flex-eland'); ?>
                            </h2>
                            
                            <div class="location-grid">
                                <?php
                                $location_taxonomies = [
                                    'district' => ['icon' => 'fas fa-map', 'label' => __('District', 'flex-eland')],
                                    'thana' => ['icon' => 'fas fa-map-pin', 'label' => __('Thana', 'flex-eland')],
                                    'land-mouza' => ['icon' => 'fas fa-vector-square', 'label' => __('Mouza', 'flex-eland')],
                                    'land-survey' => ['icon' => 'fas fa-drafting-compass', 'label' => __('Survey', 'flex-eland')]
                                ];
                                
                                foreach ($location_taxonomies as $taxonomy => $data) :
                                    $terms = get_the_terms($post->ID, $taxonomy);
                                    if ($terms && !is_wp_error($terms)) : ?>
                                        <div class="location-card">
                                            <div class="location-icon">
                                                <i class="<?php echo esc_attr($data['icon']); ?>"></i>
                                            </div>
                                            <div class="location-content">
                                                <h3><?php echo esc_html($data['label']); ?></h3>
                                                <ul class="term-list">
                                                    <?php foreach ($terms as $term) : ?>
                                                        <li>
                                                            <?php echo esc_html($term->name); ?>
                                                            <!-- <a href="<?php //echo esc_url(get_term_link($term)); ?>">
                                                                <?php //echo esc_html($term->name); ?>
                                                            </a> -->
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php endif;
                                endforeach; ?>
                            </div>
                        </section>

                        <!-- Property Details -->
                        <section class="post-section property-details">
                            <h2 class="section-title">
                                <i class="fas fa-info-circle"></i>
                                <?php esc_html_e('Permissionrmation', 'flex-eland'); ?>
                            </h2>
                            
                            <div class="details-grid">
                                <?php 
                                $property_details = [
                                    'mobile-number' => ['icon' => 'fas fa-phone', 'label' => __('Mobile Number', 'flex-eland')],
                                    'email-address' => ['icon' => 'fas fa-envelop', 'label' => __('Email Address', 'flex-eland')],
                                    'physical-address' => ['icon' => 'fas fa-map-marker-alt', 'label' => __('Physical Address', 'flex-eland')],
                                ];
                                
                                foreach ($property_details as $field => $data) :
                                    if ($value = get_post_meta($post->ID, $field, true)) : ?>
                                        <div class="detail-card">
                                            <div class="detail-icon">
                                                <i class="<?php echo esc_attr($data['icon']); ?>"></i>
                                            </div>
                                            <div class="detail-content">
                                                <h3><?php echo esc_html($data['label']); ?></h3>
                                                <p><?php echo esc_html($value); ?></p>
                                            </div>
                                        </div>
                                    <?php endif;
                                endforeach; ?>
                            </div>
                        </section>

                        <!-- Company Content -->
                        <section class="post-section post-content">
                            <h2 class="section-title">
                                <i class="fas fa-file-signature"></i>
                                <?php esc_html_e('Company Details', 'flex-eland'); ?>
                            </h2>
                            <div class="content-wrapper">
                                <?php echo apply_filters('the_content', $post->post_content); ?>
                            </div>
                        </section>

                        <!-- Attachments Gallery -->
                        <!-- <section class="post-section documents-gallery">
                            <h2 class="section-title">
                                <i class="fas fa-file-contract"></i>
                                <?php esc_html_e('Company Attachments', 'flex-eland'); ?>
                            </h2>
                            
                            <?php if ($attachments = get_post_meta($post->ID, 'post-attachments', true)) : ?>
                                <div class="documents-grid">
                                    <?php foreach ($attachments as $doc_id) : 
                                        $doc_url = wp_get_attachment_url($doc_id);
                                        $doc_title = get_the_title($doc_id);
                                        $file_type = wp_check_filetype($doc_url);
                                        $icon = self::get_file_icon($file_type['ext']);
                                        ?>
                                        <div class="document-card">
                                            <div class="document-icon">
                                                <i class="<?php echo esc_attr($icon); ?>"></i>
                                            </div>
                                            <div class="document-info">
                                                <h3><?php echo esc_html($doc_title); ?></h3>
                                                <span class="file-type"><?php echo strtoupper($file_type['ext']); ?></span>
                                            </div>
                                            <a href="<?php echo esc_url($doc_url); ?>" 
                                            class="download-btn" 
                                            target="_blank" 
                                            download>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <p class="no-documents"><?php esc_html_e('No documents available', 'flex-eland'); ?></p>
                            <?php endif; ?>
                        </section> -->
                    </div>

                    <!-- Sidebar Column -->
                    <aside class="post-sidebar">
                        <!-- Quick Facts -->
                        <!-- <div class="sidebar-widget quick-facts">
                            <h3 class="widget-title">
                                <i class="fas fa-bolt"></i>
                                <?php esc_html_e('Quick Facts', 'flex-eland'); ?>
                            </h3>
                            <div class="facts-grid">
                                <div class="fact-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div>
                                        <span class="fact-label"><?php esc_html_e('Posted', 'flex-eland'); ?></span>
                                        <span class="fact-value"><?php echo human_time_diff(get_the_time('U', $post), current_time('timestamp')) . ' ago'; ?></span>
                                    </div>
                                </div>
                                
                                <?php if ($last_updated = get_the_modified_time('U', $post)) : ?>
                                    <div class="fact-item">
                                        <i class="fas fa-sync-alt"></i>
                                        <div>
                                            <span class="fact-label"><?php esc_html_e('Updated', 'flex-eland'); ?></span>
                                            <span class="fact-value"><?php echo human_time_diff($last_updated, current_time('timestamp')) . ' ago'; ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div> -->

                        <!-- Attachments Gallery Widget -->
                        <?php if ($attachments = get_post_meta($post->ID, 'post-attachments', true)) : ?>
                            <div class="sidebar-widget documents-gallery">
                                <h3 class="widget-title">
                                    <i class="fas fa-file-contract"></i>
                                    <?php esc_html_e('Company Attachments', 'flex-eland'); ?>
                                </h3>
                                <div class="documents-grid">
                                    <?php foreach ($attachments as $doc_id) : 
                                        $doc_url = wp_get_attachment_url($doc_id);
                                        $doc_title = get_the_title($doc_id);
                                        $file_type = wp_check_filetype($doc_url);
                                        $icon = self::get_file_icon($file_type['ext']);
                                        ?>
                                        <div class="document-card">
                                            <div class="document-icon">
                                                <i class="<?php echo esc_attr($icon); ?>"></i>
                                            </div>
                                            <div class="document-info">
                                                <h4><?php echo esc_html($doc_title); ?></h4>
                                                <span class="file-type"><?php echo strtoupper($file_type['ext']); ?></span>
                                            </div>
                                            <a href="<?php echo esc_url($doc_url); ?>" 
                                            class="download-btn" 
                                            target="_blank" 
                                            download>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="sidebar-widget documents-gallery">
                                <h3 class="widget-title">
                                    <i class="fas fa-file-contract"></i>
                                    <?php esc_html_e('Company Attachments', 'flex-eland'); ?>
                                </h3>
                                <p class="no-documents"><?php esc_html_e('No documents available', 'flex-eland'); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Image Gallery -->
                        <?php if ($images = get_post_meta($post->ID, 'post-images', true)) : ?>
                            <div class="sidebar-widget image-gallery">
                                <h3 class="widget-title">
                                    <i class="fas fa-images"></i>
                                    <?php esc_html_e('Image Gallery', 'flex-eland'); ?>
                                </h3>
                                <div class="gallery-grid">
                                    <?php foreach ($images as $image_id) : 
                                        $image_url = wp_get_attachment_image_url($image_id, 'medium');
                                        $image_full = wp_get_attachment_image_url($image_id, 'full');
                                        ?>
                                        <a href="<?php echo esc_url($image_full); ?>" class="gallery-item" data-fancybox="post-gallery">
                                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title($image_id)); ?>">
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Related Companys -->
                        <?php 
                        $mouza_terms = get_the_terms($post->ID, 'land-mouza');
                        if ($mouza_terms && !is_wp_error($mouza_terms)) :
                            $related_args = [
                                'post_type' => 'flpost',
                                'posts_per_page' => 3, // Reduced from 4 to 3
                                'post__not_in' => [$post->ID],
                                'tax_query' => [
                                    [
                                        'taxonomy' => 'land-mouza',
                                        'field' => 'term_id',
                                        'terms' => wp_list_pluck($mouza_terms, 'term_id')
                                    ]
                                ]
                            ];
            
                            $related_posts = new \WP_Query($related_args);
                            
                            if ($related_posts->have_posts()) : ?>
                                <div class="sidebar-widget related-posts">
                                    <h3 class="widget-title">
                                        <i class="fas fa-link"></i>
                                        <?php esc_html_e('Related Companys', 'flex-eland'); ?>
                                    </h3>
                                    <div class="related-grid">
                                        <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                                            <a href="<?php the_permalink(); ?>" class="related-card">
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <div class="related-thumbnail">
                                                        <?php the_post_thumbnail('thumbnail'); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="related-content">
                                                    <h4><?php the_title(); ?></h4>
                                                    <?php if ($post_number = get_post_meta(get_the_ID(), 'post-number', true)) : ?>
                                                        <span class="post-number"><?php echo esc_html($post_number); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </a>
                                        <?php endwhile; wp_reset_postdata(); ?>
                                    </div>
                                </div>
                            <?php endif;
                        endif; ?>
                    </aside>
                </div>
            </article>
        </main>
        <?php
        
        // Reset post data
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    /**
     * Get Font Awesome icon for a file extension
     * 
     * @param string $extension The file extension (without dot)
     * @return string Font Awesome icon class
     */
    public static function get_file_icon($extension) 
    {
        $extension = strtolower($extension);
        
        $icon_map = [
            // Attachments
            'pdf'   => 'fas fa-file-pdf',
            'doc'   => 'fas fa-file-word',
            'docx'  => 'fas fa-file-word',
            'odt'   => 'fas fa-file-alt',
            'txt'   => 'fas fa-file-alt',
            'rtf'   => 'fas fa-file-alt',
            
            // Spreadsheets
            'xls'   => 'fas fa-file-excel',
            'xlsx'  => 'fas fa-file-excel',
            'ods'   => 'fas fa-file-excel',
            'csv'   => 'fas fa-file-csv',
            
            // Presentations
            'ppt'   => 'fas fa-file-powerpoint',
            'pptx'  => 'fas fa-file-powerpoint',
            'odp'   => 'fas fa-file-powerpoint',
            
            // Archives
            'zip'   => 'fas fa-file-archive',
            'rar'   => 'fas fa-file-archive',
            '7z'    => 'fas fa-file-archive',
            'tar'   => 'fas fa-file-archive',
            'gz'    => 'fas fa-file-archive',
            
            // Images
            'jpg'   => 'fas fa-file-image',
            'jpeg'  => 'fas fa-file-image',
            'png'   => 'fas fa-file-image',
            'gif'   => 'fas fa-file-image',
            'webp'  => 'fas fa-file-image',
            'svg'   => 'fas fa-file-image',
            'bmp'   => 'fas fa-file-image',
            
            // Audio/Video
            'mp3'   => 'fas fa-file-audio',
            'wav'   => 'fas fa-file-audio',
            'ogg'   => 'fas fa-file-audio',
            'mp4'   => 'fas fa-file-video',
            'mov'   => 'fas fa-file-video',
            'avi'   => 'fas fa-file-video',
            'mkv'   => 'fas fa-file-video',
            
            // Code
            'php'   => 'fas fa-file-code',
            'html'  => 'fas fa-file-code',
            'css'   => 'fas fa-file-code',
            'js'    => 'fas fa-file-code',
            'json'  => 'fas fa-file-code',
            'xml'   => 'fas fa-file-code',
            
            // Other
            'exe'   => 'fas fa-file-download',
            'dmg'   => 'fas fa-file-download',
        ];
        
        // Return specific icon if found, otherwise generic file icon
        return $icon_map[$extension] ?? 'fas fa-file';
    }
}

?>