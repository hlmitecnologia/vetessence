# Política de Segurança

## Reportando uma Vulnerabilidade

Levamos a segurança do VetEssence a sério. Se você encontrou uma vulnerabilidade de segurança, por favor, reporte-a através dos canais abaixo.

**Por favor, NÃO abra uma issue pública para reportar vulnerabilidades de segurança.**

### Canais para Reporte

- **E-mail:** segurança@vetessence.com.br
- **GitHub:** Utilize a opção "Report a vulnerability" na aba Security do repositório

### O que incluir no reporte

- Descrição clara do problema
- Passos para reproduzir
- Versão do sistema afetada
- Impacto potencial
- Sugestão de correção (se aplicável)

### Nosso compromisso

- Responderemos ao reporte em até **48 horas úteis**
- Trabalharemos na correção e manteremos você informado do progresso
- Após a correção, publicaremos um advisory de segurança e creditaremos o reporte (se autorizado)

### Divulgação Responsável

Solicitamos que aguarde a publicação da correção antes de divulgar publicamente a vulnerabilidade.

---

## Práticas de Segurança Adotadas

- **LGPD:** Dados pessoais são anonimizados mediante solicitação
- **Senhas:** Armazenadas com hash bcrypt
- **HTTPS:** Obrigatório em produção (Let's Encrypt via Certbot)
- **Headers de Segurança:** CSP, X-Frame-Options, X-Content-Type-Options configurados no Nginx
- **Rate Limiting:** Aplicado em rotas sensíveis (login, API, verificação de receitas)
- **Audit Trail:** Logs de auditoria para ações em dados sensíveis
- **Backup:** Backup diário do banco de dados com criptografia

### Dependências

Mantemos as dependências atualizadas e utilizamos `composer audit` regularmente para identificar vulnerabilidades conhecidas.
