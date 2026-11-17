# Prompts para o gerenciamento de noticias

1. Pedir pra IA analisar o projeto no estado atual

Analise todas as classes utilizadas até o momento (contidas na pasta src), em especial
a classe NoticiaServico e a classe Noticia.

Veja também as páginas administrativas, em especial usuários.php e categorias.php.

Identifique padrões, como nomes de métodos, propriedades, variáveis, arquivos, namespaces.

Aguarde o próximo comando.


---

2. Criação do método listarTodos

Crie na classe NoticiaServico o método listarTodos, de maneira semelhante ao que foi feito em CategoriaServico e UsuarioServico.

Atenção: a consulta SQL deve no caso de usuários admin, carregar todas as noticias incluindo o nome do autor delas. Se for um usuário editor, a consulta deve carregar apenas as noticias dele próprio, sem incluir o nome do autor.










Tarefas:

- visualizar noticias e nome do autor delas (admin)
- visualizar noticias próprias (editor)


---

# Prompt – Desenvolvimento da funcionalidade de exclusão de notícias do Microblog

Atue como um desenvolvedor PHP especializado em PHP, MySQL, HTML, CSS e JavaScript.

Antes de começar, analise toda a estrutura do projeto Microblog. Utilize o arquivo `AGENTS.md` como orientação, caso exista. Respeite os padrões de organização, nomenclatura e programação já utilizados no projeto.

## 1. Objetivo
Implementar a funcionalidade de exclusão de notícias no sistema Microblog.

A implementação envolve:

- Criar um novo método responsável pela exclusão de notícias.
- Programar a página `noticia-exclui`.
- Integrar a exclusão ao banco de dados MySQL.
- Respeitar as permissões dos usuários ADMIN e EDITOR.
- Realizar testes práticos antes de efetuar o commit.

## 2. Análise inicial
Antes de programar:

1. Analise a estrutura de pastas e arquivos do projeto.
2. Consulte o arquivo `AGENTS.md`, caso exista.
3. Identifique onde estão implementados os métodos de cadastro, consulta e atualização de notícias.
4. Verifique a conexão com o banco de dados.
5. Analise a estrutura da tabela de notícias e seus relacionamentos.
6. Verifique como funcionam os níveis de acesso ADMIN e EDITOR.
7. Identifique como as demais páginas administrativas realizam operações e exibem mensagens de sucesso ou erro.
Não crie estruturas desnecessárias e mantenha a compatibilidade com o restante do sistema.

## 3. Criar o novo método de exclusão
Implemente um novo método responsável por excluir uma notícia do banco de dados.

O método deverá:

- Receber o ID da notícia que será excluída.
- Validar se o ID recebido é válido.
- Verificar se a notícia existe no banco de dados.
- Executar a exclusão utilizando consulta preparada (PDO ou MySQLi, conforme o padrão do projeto).
- Tratar possíveis erros durante a operação.
- Retornar um resultado que permita identificar se a exclusão foi realizada com sucesso.
- Verificar possíveis relacionamentos com outras tabelas antes de excluir o registro.
Se houver imagens associadas à notícia, tratar os arquivos conforme o padrão do projeto, evitando apagar imagens que ainda estejam sendo utilizadas por outras notícias.

## 4. Programar a página noticia-exclui
Desenvolva a página `noticia-exclui`, respeitando o padrão visual e estrutural das demais páginas administrativas.

A página deverá:

1. Receber o ID da notícia selecionada.
2. Consultar o banco de dados para verificar se a notícia existe.
3. Apresentar as informações da notícia que será excluída.
4. Solicitar confirmação antes de executar a exclusão.
5. Executar a exclusão somente após a confirmação do usuário.
6. Utilizar o novo método de exclusão.
7. Exibir uma mensagem de sucesso após a operação.
8. Exibir mensagens adequadas caso ocorra algum erro.
9. Redirecionar o usuário para a página de gerenciamento de notícias após a conclusão.
A exclusão deverá ser realizada por meio de uma requisição POST, com proteção CSRF seguindo o padrão de segurança já utilizado no projeto.

Não permita que uma simples abertura de URL ou requisição GET exclua uma notícia.

## 5. Segurança e permissões
Garanta que:

- Somente usuários autenticados e autorizados possam excluir notícias.
- As permissões de ADMIN e EDITOR sejam respeitadas.
- O ID da notícia seja validado no servidor.
- As consultas SQL utilizem parâmetros preparados.
- Exista proteção contra CSRF.
- As informações apresentadas na confirmação sejam protegidas contra XSS.
- Não seja possível excluir uma notícia inexistente.
- A operação não seja executada sem confirmação.
- O sistema trate corretamente possíveis erros de integridade referencial.
Verifique as regras de autorização já existentes. Não invente novas permissões nem permita que um usuário ultrapasse seu nível de acesso.

## 6. Testes obrigatórios
ATENÇÃO: os testes de exclusão deverão ser realizados com notícias novas cadastradas especificamente para essa finalidade.

**PRIMEIRA ETAPA – Cadastrar notícias para testes**

Antes de testar a exclusão:

1. Acesse o sistema utilizando um usuário ADMIN.
2. Cadastre uma nova notícia com título identificável, por exemplo: `TESTE EXCLUSÃO ADMIN`.
3. Confirme se a notícia foi cadastrada corretamente.
4. Acesse o sistema utilizando um usuário EDITOR.
5. Cadastre outra notícia com título identificável, por exemplo: `TESTE EXCLUSÃO EDITOR`.
6. Confirme se a segunda notícia também foi cadastrada corretamente.
7. Registre os IDs das duas notícias cadastradas.
É obrigatório cadastrar pelo menos duas notícias novas, uma pelo ADMIN e outra pelo EDITOR.

**SEGUNDA ETAPA – Testar a exclusão**

Realize os seguintes testes:

**Teste 1 – Exclusão pelo ADMIN:** tente excluir a notícia cadastrada pelo ADMIN e verifique se a operação foi concluída corretamente.

**Teste 2 – Exclusão pelo EDITOR:** tente excluir a notícia cadastrada pelo EDITOR e verifique se a operação foi concluída corretamente, respeitando as permissões do sistema.

**Teste 3 – Cancelamento:** inicie o processo de exclusão e cancele a confirmação. Verifique se a notícia permanece cadastrada.

**Teste 4 – ID inexistente:** tente acessar a página de exclusão utilizando um ID que não existe.

**Teste 5 – ID inválido:** tente enviar valores inválidos no identificador da notícia.

**Teste 6 – Requisição GET:** tente acessar diretamente a URL de exclusão sem confirmação e verifique se a notícia não é apagada.

**Teste 7 – Segurança CSRF:** verifique se uma requisição sem token válido é rejeitada.

**Teste 8 – Permissões:** tente realizar a exclusão com um usuário sem autorização e confirme que o sistema bloqueia a operação.

**Teste 9 – Banco de dados:** consulte o MySQL e confirme que as notícias excluídas não existem mais na tabela.

**Teste 10 – Integridade do sistema:** verifique se o cadastro, a consulta e a atualização de notícias continuam funcionando normalmente.

IMPORTANTE: utilize somente as duas notícias criadas especificamente para os testes. Não exclua notícias reais ou registros já existentes no banco de dados.

## 7. Correções de possíveis problemas
Caso algum teste apresente falhas:

1. Identifique a origem do problema.
2. Analise os arquivos envolvidos.
3. Corrija o código responsável pela falha.
4. Execute novamente o teste que apresentou erro.
5. Repita os testes relacionados para garantir que a correção não prejudicou outras funcionalidades.
6. Continue corrigindo até que todos os testes executáveis sejam aprovados.
Não ignore erros nem considere a implementação concluída enquanto existirem falhas conhecidas.

Se não for possível executar algum teste por falta de ambiente, banco de dados ou credenciais, informe a limitação claramente. Não simule resultados nem declare testes como aprovados sem executá-los.

## 8. Commit no Git
Somente depois de concluir a implementação e confirmar que todos os testes executáveis foram aprovados:

1. Verifique os arquivos criados e modificados.
2. Confira se não existem erros de sintaxe ou problemas identificados.
3. Verifique o estado atual do Git.
4. Preserve alterações anteriores que não façam parte desta funcionalidade.
5. Adicione somente os arquivos relacionados à implementação da exclusão.
6. Realize o commit com a seguinte mensagem:
`feat: implementar exclusão de notícias`

Não inclua arquivos desnecessários no commit.

Não faça push para o GitHub sem autorização.

Se os testes não forem aprovados, não faça o commit. Realize as correções necessárias e repita os testes.

## 9. Atualização do arquivo prompts.md
Salve este prompt no arquivo `prompts.md`, localizado na pasta principal do projeto Microblog.

Caso o arquivo já exista, preserve os prompts anteriores e acrescente este novo prompt ao final, utilizando um título específico para a funcionalidade de exclusão de notícias.

Não apague nem substitua o conteúdo anterior.

---

# PROMPT – Desenvolvimento das páginas públicas do Microblog

Atue como um desenvolvedor PHP especializado em PHP, MySQL, HTML e CSS.

## 1. Análise inicial do projeto
Antes de começar a programação:

- Analise toda a estrutura atual do projeto Microblog.
- Verifique se existe um arquivo `AGENTS.md` e utilize suas orientações.
- Analise os arquivos PHP, CSS, banco de dados e demais estruturas existentes.
- Identifique os padrões de programação já utilizados no projeto.
- Reaproveite os métodos, funções, conexões e componentes existentes.
- Não crie estruturas desnecessárias e não altere funcionalidades que já estão funcionando.

## 2. Objetivo
Programar o funcionamento das páginas públicas do Microblog utilizando PHP, MySQL, HTML e CSS, com consultas dinâmicas ao banco de dados.

As páginas devem apresentar as notícias cadastradas no sistema, respeitando as categorias, os destaques e as informações dos autores.

## 3. Ordem de desenvolvimento

### Etapa 1 – Cabeçalho e menu de categorias
Comece programando o arquivo:

`includes/cabecalho.php`

O menu deve:

- Buscar automaticamente todas as categorias existentes no banco de dados.
- Exibir cada categoria no menu do site.
- Criar links dinâmicos para `noticias-por-categoria.php`.
- Utilizar o identificador da categoria na URL.
- Atualizar automaticamente quando uma nova categoria for cadastrada.
- Evitar categorias duplicadas.
Não cadastre categorias manualmente no HTML.

### Etapa 2 – Página inicial (`index.php`)
Programar a página inicial para apresentar:

**Notícias em destaque:**

- Buscar as notícias marcadas como destaque no banco de dados.
- Exibir a imagem, o título e o resumo de cada notícia.
- Criar um link dinâmico para `noticia.php` ao clicar na notícia.
- Utilizar o identificador correto da notícia.
**Todas as notícias:**

- Implementar a listagem utilizando o arquivo `includes/todas.php`.
- Buscar as notícias cadastradas no banco de dados.
- Exibir imagem, título e resumo.
- Criar links para acessar os detalhes de cada notícia.

### Etapa 3 – Organização das notícias em duas colunas
**ATENÇÃO: O site deve exibir somente DUAS notícias lado a lado, nunca três.**

Aplicar essa organização visual nas listagens de notícias da página inicial e nas demais páginas públicas que exibem cards de notícias.

Requisitos:

- Utilizar duas colunas em telas de computador.
- Não permitir três cards na mesma linha.
- Manter as imagens com o mesmo tamanho e proporção.
- Utilizar espaçamento adequado entre os cards.
- Manter títulos e resumos organizados.
- Garantir que os cards tenham alinhamento visual consistente.
- Em dispositivos móveis, apresentar apenas uma notícia por linha.
- Utilizar CSS Grid ou Flexbox, respeitando os estilos já existentes no projeto.
Exemplo de organização CSS:

```css
.lista-noticias {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}

.card-noticia img {
    width: 100%;
    aspect-ratio: 16 / 9;
    object-fit: cover;
}

@media (max-width: 600px) {
    .lista-noticias {
        grid-template-columns: 1fr;
    }
}
```
Adapte os nomes das classes ao padrão existente no projeto. Não duplique estilos desnecessariamente.

### Etapa 4 – Página de detalhes da notícia (`noticia.php`)
Programar a página para apresentar os detalhes completos de uma notícia.

Exibir:

- Título da notícia.
- Data de publicação no formato `dd/mm/aaaa`.
- Nome do autor.
- Imagem da notícia.
- Texto completo da notícia.
Regras:

- Buscar a notícia pelo identificador recebido pela URL.
- Se não existir um autor associado à notícia, exibir `"Equipe Microblog"`.
- Se a notícia não existir, apresentar uma mensagem adequada.
- Não exibir informações de outra notícia quando o identificador for inválido.

### Etapa 5 – Notícias por categoria (`noticias-por-categoria.php`)
Programar a página para apresentar as notícias de acordo com a categoria selecionada no menu.

Exibir para cada notícia:

- Imagem.
- Título.
- Data de publicação no formato `dd/mm/aaaa`.
- Nome do autor.
- Resumo.
- Link dinâmico para `noticia.php`.
Regras:

- Filtrar as notícias utilizando o identificador da categoria.
- Se não existir um autor associado, exibir `"Equipe Microblog"`.
- Se a categoria não possuir notícias, apresentar uma mensagem informando que não existem notícias cadastradas.
- Manter o padrão visual de duas notícias lado a lado em computadores e uma por linha em dispositivos móveis.

## 4. Requisitos técnicos

- Utilizar PHP e MySQL.
- Realizar consultas dinâmicas ao banco de dados.
- Utilizar consultas preparadas (PDO ou MySQLi, conforme o padrão existente).
- Proteger as consultas contra SQL Injection.
- Proteger as saídas HTML contra XSS.
- Validar os identificadores recebidos pela URL.
- Utilizar `LEFT JOIN` quando necessário para permitir notícias sem autor associado.
- Respeitar os nomes reais das tabelas e colunas existentes no banco de dados.
- Reaproveitar a conexão existente com o banco de dados.
- Não inventar campos ou tabelas sem antes verificar a estrutura atual.

## 5. Testes obrigatórios
Após implementar as funcionalidades, testar:

1. Se todas as categorias aparecem automaticamente no menu.
2. Se uma nova categoria cadastrada aparece no menu sem precisar alterar o HTML.
3. Se as notícias em destaque são exibidas corretamente.
4. Se todas as notícias aparecem na página inicial.
5. Se as notícias são organizadas em exatamente duas colunas no computador.
6. Se não aparecem três notícias lado a lado.
7. Se no celular aparece apenas uma notícia por linha.
8. Se os links direcionam para a notícia correta.
9. Se a página de detalhes apresenta todas as informações.
10. Se notícias sem autor exibem `"Equipe Microblog"`.
11. Se o filtro por categoria funciona corretamente.
12. Se categorias sem notícias apresentam uma mensagem adequada.
13. Se identificadores inválidos são tratados corretamente.
14. Se não existem erros de PHP, SQL ou problemas no layout.

## 6. Correções e validação
Após finalizar a programação:

- Revise todos os arquivos modificados.
- Corrija qualquer erro encontrado.
- Verifique se as funcionalidades antigas continuam funcionando.
- Teste a navegação entre as páginas públicas.
- Verifique a organização visual em diferentes tamanhos de tela.
- Não considere a tarefa concluída enquanto houver erros.

## 7. Registro do prompt
Salve este prompt no arquivo `prompts.md`, localizado na raiz do projeto.

- Se o arquivo não existir, crie-o.
- Se já existir, acrescente este prompt ao final.
- Preserve integralmente os prompts anteriores.
- Não apague nem substitua conteúdos existentes.

## 8. Relatório final
Ao terminar, informe:

- Quais arquivos foram criados ou modificados.
- Quais funcionalidades foram implementadas.
- Quais testes foram realizados.
- Se todos os testes passaram.
- Se algum problema foi encontrado e como foi corrigido.
- Se o arquivo `prompts.md` foi atualizado.

**IMPORTANTE:** Execute o desenvolvimento seguindo a ordem das etapas. Não pule a implementação do cabeçalho e do menu dinâmico de categorias. Mantenha o padrão visual do projeto e garanta que as notícias sejam exibidas em apenas duas colunas, nunca três.

## 10. Relatório final
Ao finalizar, apresente um relatório contendo:

- Arquivos criados.
- Arquivos modificados.
- Método de exclusão implementado.
- Funcionamento da página `noticia-exclui`.
- Confirmação de que as duas notícias de teste foram cadastradas.
- IDs das notícias utilizadas nos testes.
- Resultado dos testes de exclusão do ADMIN e EDITOR.
- Resultado dos demais testes de segurança.
- Correções realizadas.
- Situação do arquivo `prompts.md`.
- Situação do commit.

**IMPORTANTE:** Execute as etapas na ordem estabelecida: primeiro implemente, depois cadastre as duas notícias de teste, em seguida tente excluí-las, realize as demais verificações e somente então faça o commit, caso tudo esteja funcionando corretamente.