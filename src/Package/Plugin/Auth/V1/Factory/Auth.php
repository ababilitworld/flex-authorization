<?php
// factories/AuthorizationFactory.php
class AuthorizationFactory {
    public static function create(string $driver = 'database'): AuthorizationContract {
        switch ($driver) {
            case 'database':
                return self::createDatabaseAuthorization();
            // case 'api': future implementations
            default:
                throw new InvalidArgumentException("Unsupported driver: {$driver}");
        }
    }

    private static function createDatabaseAuthorization(): AuthorizationContract {
        $permissionRepository = new DatabasePermissionRepository();
        $roleRepository = new DatabaseRoleRepository();
        $userRepository = new DatabaseUserRepository();

        $permissionManager = new DatabasePermissionManager($permissionRepository);
        $roleManager = new DatabaseRoleManager($roleRepository);
        $userManager = new DatabaseUserManager($roleRepository, $permissionRepository);

        return new AuthorizationService(
            $permissionManager,
            $roleManager,
            $userManager
        );
    }
}