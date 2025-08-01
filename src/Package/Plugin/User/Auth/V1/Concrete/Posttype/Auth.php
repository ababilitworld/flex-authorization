<?php 
namespace Ababilithub\FlexAuthorization\Package\Plugin\User\Auth\V1\Concrete\Posttype;

(defined( 'ABSPATH' ) && defined( 'WPINC' )) || exit();

use Ababilithub\{
    FlexAuthorization\Package\Plugin\User\Auth\V1\Base\Auth as BaseAuth,
};

class Auth extends BaseAuth 
{
    public function init_hooks(): void 
    {
        parent::init_hooks();
        add_action('pre_get_posts', [$this, 'filter_query']);
    }

    public function filter_query(\WP_Query $query): void 
    {
        if (!is_admin() || !$query->is_main_query()) 
        {
            return;
        }

        $screen = get_current_screen();
        if ($screen->post_type !== $this->item_handler->get_item_type()) 
        {
            return;
        }

        if (current_user_can($this->config['capability'])) 
        {
            return;
        }

        $user_id = get_current_user_id();
        $user_items = $this->item_handler->get_user_items($user_id);

        if ($this->config['include_authored']) 
        {
            $authored_items = get_posts([
                'post_type' => $this->item_handler->get_item_type(),
                'author' => $user_id,
                'fields' => 'ids',
                'posts_per_page' => -1
            ]);
            $user_items = array_merge($user_items, $authored_items);
        }

        if (!empty($user_items)) 
        {
            $query->set('post__in', $user_items);
        } 
        else
        {
            $query->set('post__in', [0]);
        }
    }
}