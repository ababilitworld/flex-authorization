<?php 
namespace Ababilithub\FlexAuthorization\Package\Plugin\Audit\V1\Contract;

interface Audit
{
    public function init(array $data = []): static;
    public function register(): void;
    public function render(): void;
     
}