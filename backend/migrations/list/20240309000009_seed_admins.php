<?php


return new class {
    public function up($schema)
    {
        $connection = $schema->getConnection();

        $superAdminEmail = 'admin@example.com';
        $superAdminPassword = password_hash('admin123', PASSWORD_BCRYPT);

        $connection->table('users')->updateOrInsert(
            ['email' => $superAdminEmail],
            [
                'name' => 'Super Admin',
                'password' => $superAdminPassword,
                'role' => 'super_admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );

        echo "Super Admin seeded.\n";
    }

    public function down($schema)
    {
        $schema->getConnection()->table('users')->where('email', 'admin@example.com')->delete();
    }
};
