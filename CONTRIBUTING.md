# Contribuindo com o VetEssence

Obrigado por considerar contribuir com o VetEssence! Este guia estabelece as diretrizes para contribuições.

## Como contribuir

### 1. Reportando Bugs

Antes de reportar um bug:
- Verifique se já não foi reportado nas [issues](https://github.com/hlmitecnologia/vetessence/issues)
- Utilize o template de **Bug Report** ao abrir a issue
- Inclua passos para reproduzir, ambiente (SO, PHP, banco) e logs relevantes

### 2. Sugerindo Funcionalidades

- Utilize o template de **Feature Request**
- Descreva o problema que a funcionalidade resolve
- Se possível, inclua exemplos de uso e referências

### 3. Pull Requests

#### Passo a passo

1. **Fork** o repositório
2. Crie um branch descritivo: `git checkout -b feat/minha-feature` ou `fix/meu-bug`
3. Faça as alterações seguindo os padrões do código existente
4. Escreva ou atualize testes
5. Execute os testes localmente: `php artisan test`
6. Commit com mensagem clara (inglês ou português técnico)
7. Push para seu fork e abra um Pull Request

#### Padrões de código

- **Linguagem:** Código em inglês (variáveis, funções, comentários)
- **Interface:** UI em português brasileiro
- **Estilo:** Seguimos PSR-12 e as convenções do Laravel
- **Testes:** Toda funcionalidade nova deve incluir testes
- **Commits:** Mensagens descritivas, preferencialmente em inglês técnico

#### O que esperar

- Revisão de código por pelo menos um mantenedor
- Pode ser solicitado ajustes antes do merge
- Pull Requests para a branch `main` com proteção contra pushes diretos

### 4. Tradução e Documentação

Contribuições com documentação (traduções, melhorias no README, guias) são bem-vindas. Consulte a seção de issues marcadas com `documentation`.

## Ambiente de Desenvolvimento

```bash
cp .env.example .env
composer install
npm install && npm run dev
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Testes

```bash
php artisan test
# ou para um arquivo específico
php artisan test tests/Feature/MyTest.php
```

Ver [docs/performance.md](docs/performance.md) para otimizações de ambiente.

---

## Código de Conduta

Projetamos manter um ambiente acolhedor e respeitoso para todos os contribuidores. Não será tolerado nenhum tipo de assédio ou discriminação.

## Dúvidas

Abra uma [discussion](https://github.com/hlmitecnologia/vetessence/discussions) ou entre em contato pelo [WhatsApp](https://wa.me/5511999999999).
