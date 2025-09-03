#!/bin/bash

# Script de build personalizado para o Netlify
set -e

echo "🚀 Iniciando build no Netlify..."

# Verificar versões
echo "📋 Versões das ferramentas:"
node --version
npm --version

# Limpar cache do npm
echo "🧹 Limpando cache do npm..."
npm cache clean --force

# Instalar dependências
echo "📦 Instalando dependências..."
npm install --legacy-peer-deps

# Verificar se as dependências foram instaladas
echo "✅ Dependências instaladas:"
ls -la node_modules/

# Build do projeto
echo "🔨 Executando build..."
npm run build

# Verificar se o build foi criado
echo "📁 Conteúdo do diretório public:"
ls -la public/

echo "🎉 Build concluído com sucesso!"
