<?php
require_once './PDOConfig.php';

#Verificando se foi passado o id do usuário como parâmetro
if((is_int((int) $argv[1])) ) {
    
    #id do usuário
    $user = (int) $argv[1];
    
    #Instânciando a classe de conexão classe de conexão
    $conexao = new PDOConfig();
    
   #String da consulta
    $stringConsulta = "select chamada.*, consulta.*, cliente.*, estadual.*, regional.*, agencia.* , estado.*, st.*, sub.* from chamada_cliente chamada"
            . " INNER JOIN consulta_cliente consulta on consulta.id = chamada.id_onsulta_cliente "
            . " INNER JOIN users user on user.id = consulta.user_id_user "
            . " INNER JOIN status st on st.id_status = chamada.status_id_status "
            . " INNER JOIN subrotinas sub on sub.id_subrotina = chamada.subrotinas_id_subrotina "
            . " INNER JOIN clientes cliente on cliente.id_cliente = consulta.clientes_id_cliente "
            //. " INNER JOIN limite_credito_novo limite on cliente.LimiteCreditoNovo = limite.id_limite_credito_novo "
            . " INNER JOIN super_estadual estadual on estadual.id_super_estadual = cliente.super_estadual_id_super_estadual "
            . " INNER JOIN super_regional regional on regional.id_super_regional = cliente.super_regional_id_super_regional "
            . " INNER JOIN ag agencia on agencia.id_ag = cliente.ag_id_ag "
            . " INNER JOIN uf estado on estado.id = agencia.id_uf "
            . " where consulta.status_arquivo_retorno != 1 AND chamada.status_chamada = 1 AND user.id = {$user} ";

    #Statement de consulta
    if(count($argv) == 4) {
        $stringConsulta .= " AND date(chamada.data_pendencia) between '{$argv[2]}' AND '{$argv[3]}' ";
    } else {
        $stringConsulta .= " AND date(chamada.data_pendencia) = CURDATE() ";
    }

    #Statement de consulta
    $stm =  $conexao->prepare($stringConsulta);
      
    #Executendo a consulta
    $stm->execute();

    #Recuperando todos os dados do resultado
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);
    
    #Data de hoje
    $hoje = new \DateTime("now");
    
    #Arquivo de retorno
    $file_path = "arquivo_retorno_{$hoje->format("dmYHis")}.csv"; 

    #string dos dados de retorno
    $stringRetorno = "";

    #Processamento dos resultados e criação do arquivo de retorno
    for($i = 0; $i < count($result); $i++) {        
        $stringRetorno .= "{$result[$i]['coc_cliente']};";
        $stringRetorno .= "{$result[$i]['mci_correspondente_cliente']};";
        $stringRetorno .= "{$result[$i]['sexos_id_sexo']};";
        $stringRetorno .= "{$result[$i]['mci_empregador']};";
        $stringRetorno .= "1;";
        //$stringRetorno .= "{$result[$i]['limite_credito_novo']};";
        $stringRetorno .= "{$result[$i]['uf']};";
        $stringRetorno .= "{$result[$i]['cod_super_estadual']};";
        $stringRetorno .= "{$result[$i]['cod_super_regional']};";
        $stringRetorno .= "{$result[$i]['prefixo_ag']};";
        $stringRetorno .= "{$result[$i]['coc_cliente']};";
        $stringRetorno .= "{$result[$i]['conta_corrente']};";
        $stringRetorno .= "{$result[$i]['nome_cliente']};";
        $stringRetorno .= "{$result[$i]['cpf_cliente']};";
        $stringRetorno .= "{$result[$i]['mci_cliente']};";
        $stringRetorno .= "{$result[$i]['ddd_fone_resid_cliente']};";
        $stringRetorno .= "{$result[$i]['fone_resid_cliente']};";
        $stringRetorno .= "{$result[$i]['ddd_fone_comer_cliente']};";
        $stringRetorno .= "{$result[$i]['fone_comer_cliente']};";
        $stringRetorno .= "{$result[$i]['ddd_fone_cel_cliente']};";
        $stringRetorno .= "{$result[$i]['fone_cel_cliente']};";
        $stringRetorno .= "{$result[$i]['ddd_fone_pref_cliente']};";
        $stringRetorno .= "{$result[$i]['fone_pref_cliente']};";
        $stringRetorno .= "{$result[$i]['num_beneficio_cliente']};";
        $stringRetorno .= "{$result[$i]['dv_cliente']};";
        $stringRetorno .= "{$result[$i]['data_nasc_cliente']};";
        $stringRetorno .= "{$result[$i]['num_beneficio_comp_cliente']};"; 
        $stringRetorno .= utf8_encode("{$result[$i]['status']};"); 
        $stringRetorno .= utf8_encode("{$result[$i]['subrotina']};"); 
        $stringRetorno  = substr($stringRetorno, 0, -1);
        $stringRetorno .= "\n";
    }
     //var_dump($file_path);exit;
    if(!empty($stringRetorno)) {
         #Gravando o arquivo de retorno
        if(fwrite($file=fopen($file_path,'w+'),$stringRetorno)) {  
            fclose($file);  
            echo "Arquivo gravado com sucesso! \n";
        }
    } else {
        echo "Dados não encontrados \n";
    }
   
} else {
    echo "Você deve informar um id de usuário \n";
}