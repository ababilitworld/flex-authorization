<?php 
namespace Ababilithub\FlexAuthorization\Package\Plugin\Role\V1\Base;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\FlexAuthorization\Package\Plugin\Role\V1\Contract\Role as RoleContract;

abstract class Role implements RoleContract
{
    protected $role_slug;
    protected $display_name;
    protected $capabilities = [];
    protected $wp_role;
    
    protected $default_capabilities = [
        'read' => true,
        'view_admin_dashboard' => true,
    ];

    abstract public function init(array $data = []): static;
    
    public function create_role(): void
    {
        global $wp_roles;
        
        // Remove existing role if it exists
        if (isset($wp_roles->roles[$this->role_slug])) 
        {
            $wp_roles->remove_role($this->role_slug);
        }
        
        // Merge default capabilities with custom ones
        $capabilities = array_merge($this->default_capabilities, $this->capabilities);
        
        // Add the role with capabilities
        add_role($this->role_slug, $this->display_name, $capabilities);
        
        // Store the WP_Role object
        $this->wp_role = get_role($this->role_slug);
    }

    public function get_label(): string
    {
        $roles = wp_roles();
        return $roles->role_names[$this->role_slug] ?? $this->display_name;
    }

    public function add_capabilities(array $capabilities = []): void
    {
        if (!$this->wp_role) 
        {
            return;
        }

        foreach ($capabilities as $capability => $value) 
        {
            if ($value) 
            {
                $this->wp_role->add_cap($capability);
            } 
            else
            {
                $this->wp_role->remove_cap($capability);
            }
        }
    }

    public function remove_capabilities(array $capabilities = []): void
    {
        if (!$this->wp_role) 
        {
            return;
        }

        if (empty($capabilities)) 
        {
            // Remove all capabilities if none specified
            foreach ($this->wp_role->capabilities as $cap => $value) 
            {
                $this->wp_role->remove_cap($cap);
            }
        } 
        else
        {
            // Remove only specified capabilities
            foreach ($capabilities as $capability) 
            {
                $this->wp_role->remove_cap($capability);
            }
        }
    }

    public function register(): void
    {
        $this->create_role();
    }
}