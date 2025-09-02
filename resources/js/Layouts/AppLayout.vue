<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  user: Object
})

const logout = () => {
  useForm().post(route('logout'))
}
</script> 

<template>
  <v-app>
    <v-navigation-drawer
      expand-on-hover
      permanent
      rail
      app
    >
      <v-list>
        <v-list-item
          prepend-avatar="https://randomuser.me/api/portraits/women/85.jpg"
          :subtitle="user.email"
          :title="user.name"
        ></v-list-item>
      </v-list>

      <v-divider></v-divider>

      <v-list density="compact" nav>
        <v-list-item 
          prepend-icon="mdi-view-dashboard" 
          title="Dashboard" 
          value="dashboard"
          :to="route('dashboard')"
        ></v-list-item>
        <v-list-item 
          prepend-icon="mdi-format-list-checks" 
          title="Tarefas" 
          value="tasks"
          :to="route('tasks.index')"
        ></v-list-item>
        <v-list-item 
          prepend-icon="mdi-account-multiple" 
          title="Usuários" 
          value="users"
        ></v-list-item>
        <v-list-item 
          prepend-icon="mdi-star" 
          title="Favoritos" 
          value="starred"
        ></v-list-item>
      </v-list>

      <v-divider></v-divider>

      <v-list density="compact" nav>
        <v-list-item 
          prepend-icon="mdi-logout" 
          title="Sair" 
          value="logout"
          @click="logout"
        ></v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main>
      <v-container fluid>
        <slot />
      </v-container>
    </v-main>
  </v-app>
</template>

