<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Permission\PostMeta\PostMetaBoxContent\Manager;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexPhp\Package\Manager\V1\Base\Manager as BaseManager,
    FlexWordpress\Package\PostMetaBoxContent\V1\Contract\PostMetaBoxContent as PostMetaBoxContentContract, 
    FlexWordpress\Package\PostMetaBoxContent\V1\Factory\PostMetaBoxContent as PostMetaBoxContentFactory,
    FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Permission\PostMeta\PostMetaBoxContent\Concrete\Section\General\PostMetaBoxContent as GeneralSectionPostMetaBoxContent,
    FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Permission\PostMeta\PostMetaBoxContent\Concrete\Section\Image\PostMetaBoxContent as ImageSectionPostMetaBoxContent,
    FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Permission\PostMeta\PostMetaBoxContent\Concrete\Section\Attachment\PostMetaBoxContent as AttachmentSectionPostMetaBoxContent,
};

class  PostMetaBoxContent extends BaseManager
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->set_items(
                [
                GeneralSectionPostMetaBoxContent::class,
                ImageSectionPostMetaBoxContent::class,
                AttachmentSectionPostMetaBoxContent::class,
            ]
        );
    }

    public function boot(): void 
    {
        foreach ($this->get_items() as $item) 
        {
            $item_instance = PostMetaBoxContentFactory::get($item);

            if ($item_instance instanceof PostMetaBoxContentContract) 
            {
                $item_instance->register();
            }
        }
    }

    public function save_post($post_id, $post, $update): void 
    {
        foreach ($this->get_items() as $item) 
        {
            $item_instance = PostMetaBoxContentFactory::get($item);

            if ($item_instance instanceof PostMetaBoxContentContract) 
            {
                $item_instance->save($post_id, $post, $update);
            }
        }
    }
}