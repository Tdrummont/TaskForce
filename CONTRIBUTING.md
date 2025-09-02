# 🤝 Guia de Contribuição

Obrigado por considerar contribuir com o **TaskForce**! Este documento fornece diretrizes para contribuições.

## 📋 **Como Contribuir**

### **1. Fork o Projeto**
- Faça um fork do repositório principal
- Clone seu fork localmente
- Configure o upstream para sincronizar com o repositório principal

```bash
git clone https://github.com/SEU_USUARIO/taskforce.git
cd taskforce
git remote add upstream https://github.com/tdrummontt/taskforce.git
```

### **2. Crie uma Branch**
Crie uma branch para sua contribuição:

```bash
git checkout -b feature/nova-funcionalidade
# ou
git checkout -b fix/correcao-bug
# ou
git checkout -b docs/melhoria-documentacao
```

**Convenções de nomenclatura:**
- `feature/` - Novas funcionalidades
- `fix/` - Correções de bugs
- `docs/` - Melhorias na documentação
- `refactor/` - Refatoração de código
- `test/` - Adição ou correção de testes
- `chore/` - Tarefas de manutenção

### **3. Desenvolva sua Contribuição**

#### **Padrões de Código**
- **PHP**: Siga os padrões PSR-12
- **JavaScript**: Use ES6+ e siga as convenções do Vue.js
- **CSS**: Siga as convenções do Tailwind CSS
- **Comentários**: Escreva comentários claros e em português

#### **Estrutura do Projeto**
```
app/
├── Http/Controllers/    # Controllers da aplicação
├── Models/             # Modelos Eloquent
├── Events/             # Eventos do sistema
├── Notifications/      # Notificações
└── Services/           # Serviços de negócio

resources/
├── js/                 # Componentes Vue.js
├── css/                # Estilos Tailwind
└── views/              # Templates Blade
```

#### **Testes**
- Escreva testes para novas funcionalidades
- Certifique-se de que todos os testes passem
- Mantenha a cobertura de testes acima de 80%

```bash
# Executar testes
php artisan test

# Com cobertura
php artisan test --coverage
```

### **4. Commit suas Mudanças**
Use mensagens de commit claras e descritivas:

```bash
# ✅ Bom
git commit -m "feat: adiciona sistema de notificações push"

git commit -m "fix: corrige problema de drag & drop no mobile"

git commit -m "docs: atualiza instruções de instalação"

# ❌ Evite
git commit -m "fix bug"
git commit -m "update"
git commit -m "wip"
```

**Formato das mensagens:**
```
tipo(escopo): descrição curta

[corpo opcional]

[rodapé opcional]
```

**Tipos:**
- `feat`: Nova funcionalidade
- `fix`: Correção de bug
- `docs`: Documentação
- `style`: Formatação de código
- `refactor`: Refatoração
- `test`: Testes
- `chore`: Tarefas de manutenção

### **5. Push e Pull Request**
```bash
git push origin feature/nova-funcionalidade
```

Depois, crie um Pull Request no GitHub com:

- **Título descritivo** da mudança
- **Descrição detalhada** do que foi implementado
- **Screenshots** se aplicável
- **Testes** realizados
- **Checklist** de verificação

## 🔧 **Configuração do Ambiente de Desenvolvimento**

### **1. Instalação**
```bash
# Clone o repositório
git clone https://github.com/SEU_USUARIO/taskforce.git
cd taskforce

# Instale as dependências
composer install
npm install

# Configure o ambiente
cp .env.example .env
php artisan key:generate
```

### **2. Banco de Dados**
```bash
# Configure o .env com suas credenciais
# Execute as migrações
php artisan migrate
php artisan db:seed
```

### **3. Desenvolvimento**
```bash
# Backend
php artisan serve

# Frontend (em outro terminal)
npm run dev
```

## 📝 **Diretrizes de Código**

### **PHP (Laravel)**
```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    /**
     * Exibe a lista de tarefas
     */
    public function index(): JsonResponse
    {
        $tasks = Task::with(['user', 'comments'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }
}
```

### **JavaScript (Vue.js)**
```javascript
<template>
  <div class="task-card">
    <h3>{{ task.title }}</h3>
    <p>{{ task.description }}</p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Props
const props = defineProps({
  task: {
    type: Object,
    required: true
  }
})

// Computed properties
const isOverdue = computed(() => {
  return new Date(props.task.due_date) < new Date()
})
</script>
```

### **CSS (Tailwind)**
```css
/* Use classes utilitárias do Tailwind */
.task-card {
  @apply bg-white rounded-lg shadow-sm border-l-4 border-yellow-400 
         hover:shadow-md transition-shadow cursor-move;
}

/* Para estilos customizados, use @apply */
.custom-button {
  @apply px-4 py-2 bg-blue-500 text-white rounded-lg 
         hover:bg-blue-600 transition-colors;
}
```

## 🧪 **Testes**

### **Testes PHP (PHPUnit)**
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;

class TaskTest extends TestCase
{
    public function test_user_can_create_task()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/tasks', [
                'title' => 'Nova Tarefa',
                'description' => 'Descrição da tarefa',
                'priority' => 'medium'
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Nova Tarefa',
            'user_id' => $user->id
        ]);
    }
}
```

### **Testes JavaScript (Jest)**
```javascript
import { mount } from '@vue/test-utils'
import TaskCard from '@/Components/TaskCard.vue'

describe('TaskCard', () => {
  it('exibe o título da tarefa', () => {
    const task = { title: 'Tarefa Teste', description: 'Descrição' }
    const wrapper = mount(TaskCard, { props: { task } })
    
    expect(wrapper.text()).toContain('Tarefa Teste')
  })
})
```

## 📚 **Documentação**

### **Comentários de Código**
```php
/**
 * Atualiza o status de uma tarefa
 * 
 * @param Request $request
 * @param Task $task
 * @return JsonResponse|RedirectResponse
 * 
 * @throws \Exception
 */
public function updateStatus(Request $request, Task $task)
{
    // Implementação...
}
```

### **README e Documentação**
- Mantenha a documentação atualizada
- Use exemplos práticos
- Inclua screenshots quando relevante
- Documente APIs e endpoints

## 🚀 **Processo de Release**

### **1. Versionamento**
- Use [Versionamento Semântico](https://semver.org/)
- Atualize o `CHANGELOG.md`
- Crie uma tag Git

### **2. Checklist de Release**
- [ ] Todos os testes passam
- [ ] Documentação atualizada
- [ ] CHANGELOG atualizado
- [ ] Versão incrementada
- [ ] Tag Git criada
- [ ] Release no GitHub criado

## 📞 **Suporte e Comunicação**

### **Canais de Comunicação**
- **Issues**: Para bugs e sugestões
- **Discussions**: Para discussões gerais
- **Email**: tdrummontt@gmail.com

### **Código de Conduta**
- Seja respeitoso e inclusivo
- Ajude outros desenvolvedores
- Mantenha o foco no projeto
- Reporte comportamentos inadequados

## 🙏 **Agradecimentos**

Obrigado por contribuir com o **TaskForce**! Suas contribuições ajudam a tornar este projeto melhor para todos.

---

**Juntos, construímos o futuro do gerenciamento de tarefas! 🚀** 