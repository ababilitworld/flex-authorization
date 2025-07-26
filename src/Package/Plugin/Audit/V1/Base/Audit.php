<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Audit\V1\Base;

use Ababilithub\{
    FlexAuthorization\Package\Plugin\Audit\V1\Contract\Audit as AuditContract
};

abstract class Audit implements AuditContract
{
    protected $object_type;
    protected $object_id;
    protected $action_id;
    protected array $allowed_object_types = ['post', 'term', 'user', 'comment'];
    protected array $allowed_actions = ['view', 'view_details', 'view_meta', 'edit', 'delete'];
    
    public function __construct(array $data = [])
    {
        $this->init($data);
    }

    abstract public function init(array $data = []): static;
    
    public function register(): void
    {
        //
    }

    public function handleAuditRequest(): void
    {
        if (!$this->validateRequest()) 
        {
            return;
        }
        
        $this->applyAuditAction();
    }

    protected function applyAuditAction(): void
    {
        //
    }

    public function validateRequest(): bool
    {
        if ((empty($this->object_type) && !absint($this->object_id) && empty($this->action_id)))
        {
            return false;
        }

        if (!in_array($this->object_type, $this->allowed_object_types)) 
        {
            return false;
        }

        if (!in_array($this->action_id, $this->allowed_actions)) 
        {
            return false;
        }

        return $this->validateObjectExists();
    }

    protected function validateObjectExists(): bool
    {
        switch ($this->object_type) 
        {
            case 'post':
                return (bool) get_post($this->object_id);
            case 'term':
                return (bool) get_term($this->object_id);
            case 'user':
                return (bool) get_user_by('ID', $this->object_id);
            case 'comment':
                return (bool) get_comment($this->object_id);
            default:
                return false;
        }
    }
}