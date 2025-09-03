# Configuração de Validação Internacionalizada no Laravel

## 🔹 Passo 1: FormRequest Personalizado

Crie um `FormRequest` base que retorna chaves de validação:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    public function messages()
    {
        return [
            'required' => 'validation.required',
            'email' => 'validation.email',
            'min' => 'validation.min',
            'max' => 'validation.max',
            'date' => 'validation.date',
            'numeric' => 'validation.number',
            'confirmed' => 'validation.confirmed',
            'unique' => 'validation.unique',
        ];
    }
}
```

## 🔹 Passo 2: Usar no FormRequest de Tarefas

```php
<?php

namespace App\Http\Requests;

class StoreTaskRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'description' => 'nullable|max:1000',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'category' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return array_merge(parent::messages(), [
            'title.required' => 'validation.required',
            'title.max' => 'validation.max',
            'description.max' => 'validation.max',
            'due_date.date' => 'validation.date',
            'priority.in' => 'validation.invalid_priority',
        ]);
    }
}
```

## 🔹 Passo 3: Configurar no Controller

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request)
    {
        // A validação já foi feita automaticamente
        // Se falhar, retorna as chaves de validação
        
        $task = Task::create($request->validated());
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully!');
    }
}
```

## 🔹 Passo 4: Resultado no Frontend

Quando a validação falha, o Laravel retorna:

```json
{
    "errors": {
        "title": ["validation.required"],
        "email": ["validation.email"],
        "password": ["validation.min"]
    }
}
```

No Vue.js, você usa:

```vue
<div v-if="form.errors.title" class="text-red-500 text-sm">
  {{ t(form.errors.title) }}
</div>
```

Resultado:
- **PT**: "Este campo é obrigatório."
- **EN**: "This field is required."

## 🔹 Passo 5: Chaves de Validação Disponíveis

### Básicas:
- `validation.required` - Campo obrigatório
- `validation.email` - E-mail inválido
- `validation.min` - Valor muito curto
- `validation.max` - Valor muito longo
- `validation.date` - Data inválida
- `validation.number` - Número inválido

### Específicas:
- `validation.confirmed` - Confirmação não confere
- `validation.unique` - Valor já existe
- `validation.password` - Senha não atende aos critérios

## 🔹 Benefícios:

✅ **100% Internacionalizado**: Erros mudam automaticamente com o idioma
✅ **Consistente**: Mesmo padrão em toda a aplicação  
✅ **Manutenível**: Centralizado no useLocale.ts
✅ **Flexível**: Fácil adicionar novos idiomas
✅ **UX Melhorada**: Usuário vê erros no seu idioma preferido

## 🔹 Exemplo de Uso:

```vue
<template>
  <form @submit.prevent="submit">
    <input v-model="form.title" type="text" />
    
    <!-- Erro internacionalizado -->
    <div v-if="form.errors.title" class="text-red-500">
      {{ t(form.errors.title) }}
    </div>
    
    <button type="submit">Submit</button>
  </form>
</template>

<script setup>
import { useLocale } from '@/Components/useLocale'
const { t } = useLocale()

const form = useForm({
  title: '',
  description: ''
})

const submit = () => {
  form.post('/tasks', {
    onError: (errors) => {
      // Os erros já vêm como chaves de validação
      // Ex: "validation.required", "validation.email"
      console.log('Erros de validação:', errors)
    }
  })
}
</script>
```

Agora seus formulários estão 100% internacionalizados! 🚀✨
