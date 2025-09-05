declare module '@/Components/*.vue' {
  import { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component
}

declare module '@/Composables/*.ts' {
  const composable: any
  export default composable
}
