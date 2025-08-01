<?php 
namespace Ababilithub\FlexAuthorization\Package\Plugin\User\Auth\V1\Contract;
interface Auth 
{
    public function init_hooks(): void;
    public function add_user_field($user): void;
    public function save_user_items(int $user_id): bool;
    public function filter_query(\WP_Query $query): void;
}