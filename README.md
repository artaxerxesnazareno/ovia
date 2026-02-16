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

Comando para criar um model
```bash
php artisan make:model NomeDaModel
```
Comando para limpar cache do laravel e do vite
```bash
# 1. Primeiro limpe o cache do Laravel
php artisan optimize:clear

# 2. Limpe os caches do frontend (PowerShell)
Remove-Item -Recurse -Force public/build -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force node_modules/.vite -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force node_modules/.cache -ErrorAction SilentlyContinue

# 3. Reinstale dependências (opcional)
npm install

npm run build


# 4. Execute o dev server
npm run dev

# 1. Pare o servidor Vite (Ctrl+C se estiver rodando)

# 2. Compile os assets
npm run build

# 3. Ou inicie o servidor de desenvolvimento
npm run dev

# 4. Em outro terminal, inicie o Laravel
php artisan serve
```
```bash
php artisan serve
```
