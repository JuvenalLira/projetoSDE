<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoModel extends Model
{
    use HasFactory;

    protected $fillable =['fornecedorId', 'nome', 'descricao', 'codigoDeBarras', 'peso', 'altura', 'largura', 'profundidade', 'PrecoCompra', 'PrecoVenda'];
    
}
