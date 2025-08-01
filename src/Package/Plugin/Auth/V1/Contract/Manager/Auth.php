<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Auth\V1\Contract\Manager;

interface Auth
{
    public function create(array $data);
    public function find(int $id);
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}