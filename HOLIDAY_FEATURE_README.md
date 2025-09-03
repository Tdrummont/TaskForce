# 🎯 Funcionalidade de Alerta de Feriados

Este sistema implementa alertas automáticos de feriados quando o usuário seleciona uma data de vencimento para tarefas, consultando a API da Invertexto e cacheando os resultados no Redis.

## 🚀 Funcionalidades

- ✅ Consulta automática de feriados por ano e UF
- ✅ Cache Redis para otimizar performance
- ✅ Alerta visual nas páginas de criação de tarefas
- ✅ Suporte a múltiplos idiomas (PT/EN)
- ✅ Tratamento defensivo de erros da API
- ✅ Comando Artisan para pré-carregar cache

## 📋 Pré-requisitos

1. **Redis configurado e rodando**
2. **Token da API Invertexto** (obtenha em: https://www.invertexto.com/api-feriados)

## ⚙️ Configuração

### 1. Variáveis de Ambiente

Adicione ao seu arquivo `.env`:

```bash
# Token da API Invertexto
INVERTEXTO_TOKEN=seu_token_aqui

# Garanta que está usando Redis
CACHE_DRIVER=redis
```

### 2. Configuração do Redis

Certifique-se de que o Redis está configurado em `config/cache.php`:

```php
'redis' => [
    'driver' => 'redis',
    'connection' => 'cache',
    'lock_connection' => 'default',
],
```

## 🏗️ Arquitetura

### Backend (Laravel)

- **`HolidayService`**: Serviço principal que consulta a API e gerencia cache
- **`HolidayController`**: Controller que expõe o endpoint `/api/holidays/check`
- **Cache Redis**: Armazena feriados por 7 dias (ano + UF como chave)

### Frontend (Vue/Inertia)

- **Create.vue**: Página de criação de tarefas com alerta de feriados
- **QuickTaskModal.vue**: Modal de tarefa rápida com alerta de feriados
- **useLocale.ts**: Traduções para PT/EN

## 🔌 Endpoints da API

### Verificar Feriado

```
GET /api/holidays/check?date=YYYY-MM-DD&state=UF
```

**Parâmetros:**
- `date`: Data no formato YYYY-MM-DD
- `state`: UF (2 caracteres, ex: SP, RJ)

**Resposta:**
```json
{
  "is_holiday": true,
  "holiday": {
    "date": "2025-04-21",
    "name": "Tiradentes"
  }
}
```

## 🎨 Como Usar

### 1. Criação de Tarefa

1. Acesse a página "Nova Tarefa"
2. Selecione uma data de vencimento
3. Se a data for feriado, aparecerá um alerta amarelo
4. O sistema consulta automaticamente a API da Invertexto

### 2. Tarefa Rápida

1. Clique no botão "Tarefa Rápida"
2. Selecione uma data de vencimento
3. O alerta de feriado aparecerá automaticamente

### 3. Pré-carregar Cache

Execute o comando para popular o cache:

```bash
# Carregar feriados do ano atual para SP
php artisan holidays:prime

# Carregar feriados de 2025 para SP e RJ
php artisan holidays:prime 2025 --state=SP --state=RJ
```

## 🔧 Personalização

### Alterar UF Padrão

No `Create.vue` e `QuickTaskModal.vue`, altere:

```javascript
const stateUF = ref('SP') // Mude para sua UF padrão
```

### Alterar Tempo de Cache

No `HolidayService.php`, modifique:

```php
Cache::remember($cacheKey, now()->addDays(7), function () {
    // ... lógica da API
});
```

### Alterar Endpoint da API

No `HolidayService.php`, modifique:

```php
$url = 'https://api.invertexto.com/api-feriados';
```

## 🐛 Tratamento de Erros

O sistema é resiliente e não quebra a UX em caso de:

- ❌ Token da API inválido/missing
- ❌ API indisponível
- ❌ Timeout de rede
- ❌ Formato de resposta inesperado

Em todos os casos, retorna array vazio e não exibe alertas.

## 📱 Responsividade

- ✅ Alerta adaptável para mobile/desktop
- ✅ Ícone de atenção (triângulo amarelo)
- ✅ Cores consistentes com design system
- ✅ Texto responsivo e legível

## 🌐 Internacionalização

### Português
- `holiday.alert.title`: "Atenção!"
- `holiday.alert.body`: "A data de vencimento selecionada é feriado: {name}."
- `holiday.alert.generic`: "A data de vencimento selecionada é feriado."

### Inglês
- `holiday.alert.title`: "Heads up!"
- `holiday.alert.body`: "The selected due date is a holiday: {name}."
- `holiday.alert.generic`: "The selected due date is a holiday."

## 🚀 Performance

- **Cache Redis**: Feriados ficam em cache por 7 dias
- **Consulta única**: Uma vez por ano/UF
- **Lazy loading**: Só consulta quando necessário
- **Timeout**: 10 segundos para evitar travamento

## 🔒 Segurança

- ✅ Validação de entrada (date, state)
- ✅ Sanitização de UF (uppercase)
- ✅ Rate limiting implícito via cache
- ✅ Endpoint público (não requer autenticação)

## 📊 Monitoramento

Para monitorar o uso:

```bash
# Verificar chaves de cache
redis-cli keys "holidays:*"

# Verificar TTL de uma chave
redis-cli ttl "holidays:SP:2025"
```

## 🤝 Contribuição

1. Teste com diferentes UFs
2. Verifique compatibilidade com diferentes formatos de resposta da API
3. Considere adicionar logs para debugging
4. Implemente testes unitários se necessário

## 📞 Suporte

- **API Invertexto**: https://www.invertexto.com/api-feriados
- **Documentação Laravel**: https://laravel.com/docs
- **Redis**: https://redis.io/documentation

---

**Desenvolvido com ❤️ para o TaskForce**
