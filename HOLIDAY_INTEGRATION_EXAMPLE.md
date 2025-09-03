# Exemplo de Integração da Verificação de Feriados no Frontend Vue

## Configuração Completa

### 1. Backend (Laravel) ✅ IMPLEMENTADO

- **HolidayService**: Atualizado com endpoint correto `/v1/holidays/:year`
- **HolidayController**: Criado com método `check()`
- **Rota**: `/api/holidays/check` configurada
- **Configuração**: Variáveis de ambiente configuradas

### 2. Frontend (Vue) - Exemplo de Uso

#### Exemplo 1: Verificação ao mudar data de vencimento

```vue
<template>
  <div>
    <label for="due_date">Data de Vencimento:</label>
    <input 
      type="date" 
      id="due_date" 
      v-model="dueDate"
      @change="onDueDateChange"
    />
    
    <!-- Alerta de feriado -->
    <div v-if="holidayWarning" class="alert alert-warning">
      ⚠️ {{ dueDate }} é feriado: {{ holidayWarning.name }}
      <small>({{ holidayWarning.type }})</small>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      dueDate: '',
      holidayWarning: null
    }
  },
  
  methods: {
    async onDueDateChange() {
      if (!this.dueDate) return;
      
      try {
        const params = new URLSearchParams({
          date: this.dueDate,
          state: this.selectedUF || '' // 'SP', 'RJ', etc
        });
        
        const res = await fetch(`/api/holidays/check?${params.toString()}`, {
          headers: { 'Accept': 'application/json' }
        });
        
        const data = await res.json();
        
        if (data.is_holiday) {
          this.holidayWarning = data.holiday;
          // Opcional: mostrar toast/notificação
          this.$toast?.warning(`⚠️ ${this.dueDate} é feriado: ${data.holiday.name}`);
        } else {
          this.holidayWarning = null;
        }
      } catch (e) {
        console.error('Verificação de feriado falhou:', e);
        this.holidayWarning = null;
      }
    }
  }
}
</script>
```

#### Exemplo 2: Componente de Seleção de Data com Validação

```vue
<template>
  <div class="date-picker-wrapper">
    <label for="task_date">Data da Tarefa:</label>
    <input 
      type="date" 
      id="task_date" 
      v-model="selectedDate"
      @change="validateDate"
      :class="{ 'is-holiday': isHoliday }"
    />
    
    <!-- Status da data -->
    <div v-if="dateStatus" class="date-status">
      <span v-if="isHoliday" class="text-warning">
        🎉 {{ selectedDate }} é feriado: {{ dateStatus.name }}
      </span>
      <span v-else class="text-success">
        ✅ {{ selectedDate }} é um dia útil
      </span>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    state: {
      type: String,
      default: 'SP'
    }
  },
  
  data() {
    return {
      selectedDate: '',
      dateStatus: null,
      isHoliday: false
    }
  },
  
  methods: {
    async validateDate() {
      if (!this.selectedDate) {
        this.dateStatus = null;
        this.isHoliday = false;
        return;
      }
      
      try {
        const params = new URLSearchParams({
          date: this.selectedDate,
          state: this.state
        });
        
        const res = await fetch(`/api/holidays/check?${params.toString()}`);
        const data = await res.json();
        
        this.isHoliday = data.is_holiday;
        this.dateStatus = data.holiday;
        
        // Emite evento para o componente pai
        this.$emit('date-validated', {
          date: this.selectedDate,
          isHoliday: this.isHoliday,
          holiday: data.holiday
        });
        
      } catch (e) {
        console.error('Validação de data falhou:', e);
        this.dateStatus = null;
        this.isHoliday = false;
      }
    }
  }
}
</script>

<style scoped>
.date-picker-wrapper {
  margin-bottom: 1rem;
}

.date-status {
  margin-top: 0.5rem;
  font-size: 0.875rem;
}

.is-holiday {
  border-color: #fbbf24;
  background-color: #fef3c7;
}

.text-warning {
  color: #d97706;
}

.text-success {
  color: #059669;
}
</style>
```

#### Exemplo 3: Hook Composable (Composition API)

```javascript
// composables/useHolidayCheck.js
import { ref } from 'vue'

export function useHolidayCheck() {
  const isChecking = ref(false)
  const holidayInfo = ref(null)
  const error = ref(null)
  
  const checkHoliday = async (date, state = null) => {
    if (!date) return null
    
    isChecking.value = true
    error.value = null
    
    try {
      const params = new URLSearchParams({ date })
      if (state) params.append('state', state)
      
      const res = await fetch(`/api/holidays/check?${params.toString()}`)
      const data = await res.json()
      
      holidayInfo.value = data.holiday
      return data
      
    } catch (e) {
      error.value = e.message
      console.error('Holiday check failed:', e)
      return null
    } finally {
      isChecking.value = false
    }
  }
  
  return {
    isChecking,
    holidayInfo,
    error,
    checkHoliday
  }
}
```

#### Uso do Composable:

```vue
<template>
  <div>
    <input 
      type="date" 
      v-model="date"
      @change="handleDateChange"
    />
    
    <div v-if="isChecking">Verificando...</div>
    <div v-else-if="holidayInfo" class="holiday-alert">
      🎉 {{ date }} é feriado: {{ holidayInfo.name }}
    </div>
    <div v-else-if="error" class="error">
      Erro: {{ error }}
    </div>
  </div>
</template>

<script>
import { useHolidayCheck } from '@/composables/useHolidayCheck'

export default {
  setup() {
    const { isChecking, holidayInfo, error, checkHoliday } = useHolidayCheck()
    
    const handleDateChange = async () => {
      await checkHoliday(date.value, 'SP')
    }
    
    return {
      isChecking,
      holidayInfo,
      error,
      handleDateChange
    }
  }
}
</script>
```

## Testes da API

### Endpoint de Teste
```bash
# Verificar se uma data é feriado
curl "http://localhost:8000/api/holidays/check?date=2025-12-25&state=SP"

# Resposta esperada:
{
  "is_holiday": true,
  "holiday": {
    "date": "2025-12-25",
    "name": "Natal",
    "type": "feriado",
    "level": "nacional"
  }
}
```

### Teste Direto da API Invertexto
```bash
# Teste direto (funcionando)
curl "https://api.invertexto.com/v1/holidays/2025?state=SP&token=21558|3EKZ63zMT1VGbxvtzdA33JbctDrvBIIq"

# Ou com header Authorization
curl -H "Authorization: Bearer 21558|3EKZ63zMT1VGbxvtzdA33JbctDrvBIIq" \
     "https://api.invertexto.com/v1/holidays/2025?state=SP"
```

## Configurações do Ambiente

### .env
```env
# Configuração da API Invertexto
INVERTEXTO_BASE=https://api.invertexto.com
INVERTEXTO_TOKEN=21558|3EKZ63zMT1VGbxvtzdA33JbctDrvBIIq
```

### config/services.php
```php
'invertexto' => [
    'base'  => env('INVERTEXTO_BASE', 'https://api.invertexto.com'),
    'token' => env('INVERTEXTO_TOKEN'),
],
```

## Funcionalidades Implementadas

✅ **HolidayService**: Endpoint correto `/v1/holidays/:year`  
✅ **Cache Redis**: 24h de cache para otimização  
✅ **Validação**: Métodos `isHoliday()` e `isHolidayByDate()`  
✅ **API REST**: Endpoint `/api/holidays/check`  
✅ **Tratamento de Erros**: Logs e fallbacks  
✅ **Configuração**: Variáveis de ambiente  

## Próximos Passos

1. **Integrar no formulário de criação de tarefas**
2. **Adicionar validação de feriados no backend**
3. **Implementar notificações visuais no frontend**
4. **Adicionar testes automatizados**
5. **Considerar feriados estaduais específicos**
