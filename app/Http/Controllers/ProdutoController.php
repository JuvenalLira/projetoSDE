<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Http\Requests\StoreProdutoRequest;
use App\Http\Requests\UpdateProdutoRequest;
use App\Models\Servico;
use App\Models\ServicoContratado;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProdutoController extends Controller
{

    public function __construct(Produto $produto)
    {
        $this->produto = $produto;
    }

    public function index()
    {
        $produtos = $this->produto->paginate(10);

        return view('produto.listar', [
            "produtos" => $produtos
        ]);
    }

    public function create()
    {
        try {
            $servicos = Servico::get();

            if (!empty($servicos)) {
                return view('produto.cadastrar', [
                    "servicos" => $servicos
                ]);
            } else {
                return redirect(route('feedback', [
                    "titulo" => "Falha",
                    "mensagem" => "Um erro ocorreu e não foi possível carregar as informações necessárias. Tente novamente mais tarde."
                ]));
            }
        } catch (Exception $ex) {
            Log::error($ex);

            return redirect(route('feedback', [
                "titulo" => "Falha",
                "mensagem" => "Um erro ocorreu e não foi possível carregar as informações necessárias. Tente novamente mais tarde."
            ]));
        }
    }

    public function store(StoreProdutoRequest $request)
    {
        try {
            $params = [
                "produtoId" => $request->get('telefone'),
                "fornecedorId" => $request->get('fornecedorId'),
                "nome" => $request->get('nome'),
                "descricao" => $request->get('descricao'),
                "codigoDeBarras" => $request->get('codigoDeBarras'),
                "peso" => $request->get('peso'),
                "altura" => $request->get('altura'),
                "profundidade" => $request->get('profundidade'),
                "precoCompra" => $request->get('precoCompra'),
                "precoVenda" => $request->get('precoVenda'),
                "cadastrado_porid" => Auth::user()->id,
                "atualizado_por" => Auth::user()->id
            ];

            $produto = $this->produto->create($params);

            $servicos_contratados = json_decode($request->get('servicos-contratados'));

            foreach ($servicos_contratados as $servico_contratado) {
                ServicoContratado::create([
                    "produto_id" => $produto->id,
                    "servico_id" => $servico_contratado
                ]);
            }

            return redirect(route('feedback', [
                "titulo" => "Sucesso",
                "mensagem" => "Produto cadastrado com sucesso."
            ]));
        } catch (Exception $ex) {
            Log::error($ex);

            return redirect(route('feedback', [
                "titulo" => "Falha",
                "mensagem" => "Não foi possível cadastrar o produto. Tente novamente mais tarde."
            ]));
        }
    }

    public function show($id)
    {
        try {
            $produto = $this->produto->find($id);

            if (!empty($produto)) {
                $servicos = Servico::get();

                return view('produto.detalhes', [
                    "produto" => $produto,
                    "servicos" => $servicos
                ]);
            } else {
                return redirect(route('feedback', [
                    "titulo" => "Falha",
                    "mensagem" => "Produto não encontrado."
                ]));
            }
        } catch (Exception $ex) {
            Log::error($ex);

            return redirect(route('feedback'), [
                "titulo" => "Falha",
                "mensagem" => "Não foi possível encontrar o produto. Tente novamente mais tarde."
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $produto = $this->produto->find($id);

            if (!empty($produto)) {
                $servicos = Servico::get();

                return view('produto.atualizar', [
                    "produto" => $produto,
                    "servicos" => $servicos
                ]);
            } else {
                return redirect(route('feedback', [
                    "titulo" => "Falha",
                    "mensagem" => "Produto não encontrado."
                ]));
            }
        } catch (Exception $ex) {
            Log::error($ex);

            return redirect(route('feedback', [
                "titulo" => "Falha",
                "mensagem" => "Não foi possível encontrar o produto. Tente novamente mais tarde."
            ]));
        }
    }

    public function update(UpdateProdutoRequest $request, $id)
    {
        try {
            $produto = $this->produto->find($id);

            if ($produto !== null) {
                $r_servicos_contratados = $request->get('servicos-contratados');
                $r_old_servicos_contratados = $request->get('old-servicos-contratados');

                if ($r_servicos_contratados !== $r_old_servicos_contratados) {
                    $r_servicos_contratados = json_decode($r_servicos_contratados);
                    $r_old_servicos_contratados = json_decode($r_old_servicos_contratados);

                    foreach ($r_old_servicos_contratados as $old_servico_id) {
                        if (!in_array($old_servico_id, $r_servicos_contratados)) {
                            $servicos_contratados = ServicoContratado::where('produto_id', '=', $id)->where('servico_id', '=', $old_servico_id)->take(1)->get()->first();

                            if ($servicos_contratados !== null) {
                                $servicos_contratados->delete();
                            }
                        }
                    }

                    foreach ($r_servicos_contratados as $servico_id) {
                        if (!in_array($servico_id, $r_old_servicos_contratados)) {
                            $servicos_contratados = ServicoContratado::create([
                                "produto_id" => $id,
                                "servico_id" => $servico_id,
                            ]);
                        }
                    }
                }

                $params = [
                    "nome" => $request->get('nome'),
                    "email" => $request->get('email'),
                    "telefone" => $request->get('telefone'),
                    "nome_da_empresa" => $request->get('nome-da-empresa'),
                    "atualizado_por" => Auth::user()->id
                ];

                $produto->update($params);

                return redirect(route('feedback', [
                    "titulo" => "Sucesso",
                    "mensagem" => "O produto foi atualizado com sucesso!"
                ]));
            } else {
                return redirect(route('feedback', [
                    "titulo" => "Falha",
                    "mensagem" => "Produto não encontrado.",
                ]));
            }
        } catch (Exception $ex) {
            Log::error($ex);

            return redirect(route('feedback', [
                "titulo" => "Falha",
                "mensagem" => "Não foi possível atualizar o produto. Tente novamente mais tarde."
            ]));
        }
    }

    public function destroy($id)
    {
        try {
            $produto = $this->produto->find($id);

            if($produto !== null) {
                $servicos_contratados = ServicoContratado::where('produto_id', '=', $id)->get();
    
                if($servicos_contratados !== null) {
                    foreach($servicos_contratados as $servico_contratado) {
                        $servico_contratado->delete();
                    }
                }

                $produto->delete();
                
                return redirect(route('feedback', [
                    "titulo" => "Sucesso",
                    "mensagem" => "O produto foi deletado com sucesso."
                ]));
            } else {
                return redirect(route('feedback', [
                    "titulo" => "Falha",
                    "mensagem" => "Produto não encontrado."
                ]));
            }            
        } catch (Exception $ex) {
            Log::error($ex);

            return redirect(route('feedback', [
                "titulo" => "Falha",
                "mensagem" => "Não foi possível excluir o produto. Tente novamente mais tarde."
            ]));
        }
    }

    public function search(Request $request) 
    {
        $query = $request->input('query');

        if($query == null) 
            return redirect(route('produto.listar'));
        
        $produtos = Produto::where('nome', 'like', '%'.$query.'%')->paginate(10)->withQueryString();;

        return view('produto.buscar', [
            "produtos" => $produtos,
            "query" => $query
        ]);
    }
}
