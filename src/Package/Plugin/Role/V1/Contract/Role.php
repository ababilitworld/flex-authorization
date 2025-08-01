<?php 
namespace Ababilithub\FlexAuthorization\Package\Plugin\Role\V1\Contract;

interface Role
{
    public function init(array $data=[]): static;
    public function register(): void;
}