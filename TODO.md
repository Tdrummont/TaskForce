# TODO - Comentários com menções (@usuário) no YggdraTask

## Passo 1 — Preparar base de “Projeto”
- [x] Criar definição do conceito de `Project` (já que não existe no banco)
- [ ] Criar migration `projects`
- [ ] Criar model `Project`
- [ ] Criar pivot `project_user` (membros)
- [ ] Criar model-relations `Project/users`
- [ ] Ajustar `tasks` para adicionar `project_id` (FK)
- [ ] Backfill: criar 1 Project default e atualizar tasks existentes

## Passo 2 — Criar novas tabelas de comentários
- [ ] Criar migration `comments` (UUID PK, soft deletes, replies)
- [ ] Criar migration `comment_mentions` (unique comment_id,user_id)

## Passo 3 — Models
- [ ] Criar model `Comment` e relacionamentos + `extractMentions()`

## Passo 4 — API de comentários (REST)
- [ ] Criar `CommentController` com endpoints:
  - [ ] POST `/api/tasks/{task}/comments`
  - [ ] PUT `/api/comments/{comment}`
  - [ ] DELETE `/api/comments/{comment}`
- [ ] Implementar CommonMark -> HTML sanitizado + substituição de menções
- [ ] Persistir `comment_mentions`
- [ ] Soft delete e preservar thread via `parent_id`

## Passo 5 — Events e Broadcast Pusher
- [ ] Criar events `CommentPosted`, `CommentUpdated`, `CommentDeleted`
- [ ] Garantir broadcast em canal `task.{taskId}`

## Passo 6 — Notifications (ShouldQueue)
- [ ] Criar `MentionedInComment` e `RepliedToComment`

## Passo 7 — Vue (Asana-like)
- [ ] Criar `resources/js/Components/CommentEditor.vue`
- [ ] Criar `resources/js/Components/CommentItem.vue`
- [ ] Integrar no `resources/js/Pages/Tasks/Show.vue` com replies (1 nível)
- [ ] Escutar Echo/Realtime para Posted/Updated/Deleted

## Passo 8 — Dependência e testes
- [ ] `composer require league/commonmark`
- [ ] Rodar migrations e validar fluxos

