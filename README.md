Comando para criar migration
```bash
php artisan make:migration nome_da_migration
```
Comando para rodar as migrations
```bash
php artisan migrate
```

comando para verificar o status das migrations
```bash
php artisan migrate:status
```

comando para Executar todos os seeders
```bash
php artisan db:seed
```

comando para executar uma seeder específica
```bash
php artisan db:seed --class=NomeDaSeeder
```

Comando Refrescar o banco e rodar todos os seeders
```bash
php artisan migrate:refresh --seed
```
