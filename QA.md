# Verificação da entrega

Ambiente de validação: Linux, Docker Compose, WordPress 7.1.3, WooCommerce 11.2.1, PHP 8.4 no contêiner, Chromium com Playwright 1.63.0. Projeto demonstrativo local.

## Skills

As quatro skills foram lidas e aplicadas: frontend-design (direção visual), web-design-expert (integração nativa WordPress/WooCommerce), stop-slop (textos) e Impeccable (checagem técnica). As três skills de terceiros passaram no validador estrutural; a skill local já havia sido validada na criação. O lançador Impeccable tinha perdido a permissão de execução na instalação ZIP: a permissão foi corrigida e o comando context executou. O detector do Impeccable rodou sobre o tema e retornou `[]`. Isso verifica a instalação e a execução do detector; não prova a qualidade do design por si só.

## Evidências

- 9 cenários da política de frete passaram, incluindo centavo abaixo do limite, limite exato, estado inelegível, destino exterior e valores negativos.
- 6 verificações na integração WooCommerce passaram: quatro tarifas reais e duas verificações do gateway, incluindo retentativa sem redução duplicada de estoque.
- 10 verificações de navegador passaram: catálogo com 8 itens, imagens carregadas, larguras 390/768/1440, menu e Escape, categoria, busca, variação, cupom, validação de campos, checkout móvel, pedido e ausência de erros JavaScript (alguns itens agrupam mais de uma asserção).
- Axe encontrou zero violações nas verificações WCAG A/AA executadas na home, catálogo e produto, em 390 e 1440 px. O problema de contraste do preço riscado foi corrigido antes do resultado final.
- Arquivos PHP passaram na verificação de sintaxe.
- Capturas de home desktop/móvel, carrinho, checkout desktop/móvel e confirmação do pedido foram geradas; home e checkout foram inspecionados visualmente.

Relatórios brutos: `artifacts/browser-results.json`, `artifacts/accessibility-results.json` e `artifacts/impeccable-report.json`.

## Limites

Sem certificação WCAG, teste com leitor de tela, dispositivo físico, Safari/Firefox, carga ou gateway financeiro. Não houve publicação pública. A infraestrutura local não representa hardening de uma loja em produção. O projeto usa checkout clássico; Checkout Blocks não é suportado pelo gateway próprio. A avaliação de imagens e copy não significa afiliação a marcas fotografadas.

## Publicação do código

Somente código, imagens e documentação são publicados no GitHub. Não há deploy da aplicação. As evidências selecionadas ficam em `docs/`; `artifacts/`, `.env`, `.runtime/` e `node_modules/` são ignorados. Antes do primeiro envio, a árvore preparada passou no Gitleaks 8.24.2 sem achados, e uma checagem adicional confirmou que nenhum valor das credenciais locais estava nos arquivos selecionados. Nenhum banco ou pedido do ambiente local foi incluído.

## Revisão de identidade visual

Coleção de oito imagens conceituais produzidas com imagegen, otimizadas em WebP (aproximadamente 22 a 102 KB cada). O símbolo agora representa elos e acompanha o cabeçalho, favicon, capa, produto e rodapé. Carrinho e checkout possuem navegação de etapas. Descrições e legendas específicas mantêm explícito o caráter fictício dos modelos. Os prompts estão em `assets/PROMPTS.md`.

Após a revisão, os dez grupos de testes de navegador passaram novamente, incluindo asserções adicionais da página de produto em desktop e mobile. O axe retornou zero violações nas seis combinações de página e largura avaliadas. O detector de fonte do Impeccable retornou `[]`. As capturas também foram revisadas visualmente; os testes de captura aguardam o carregamento das imagens para não registrar placeholders de lazy loading.
