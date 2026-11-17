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