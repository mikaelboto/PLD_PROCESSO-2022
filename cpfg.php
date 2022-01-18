<?php

echo "Mikael da Silva Bôto\n";
echo "Respostas Código:\n\n";

$csvFile = file('202110_CPGF.csv');

$row = 1;
$TotalValue = 0.0;
$TotalValueSecretive = 0.0;
$SecretAgency = [];
$SecretMove = [];
$SecretValue = [];
$Drawer = [];
$DrawerCarrier = [];
$WithdrawalSum = [];
$DrawerAgency = [];
$Purchases = [];
$PurchasesFav = [];

if (($handle = fopen("202110_CPGF.csv", "r")) !== FALSE) {
    $fields = fgetcsv($handle, 10000, ";");
    // print_r($fields);
    
    while (($data = fgetcsv($handle, 10000, ";")) !== FALSE) {
        $num = count($data);
        $row++;
        
        $value = floatval(str_replace(",", ".", end($data)));
        
        if($data[9] == "Sigiloso") {
            $TotalValueSecretive += $value;
            
            if(!in_array($data[3], $SecretAgency)) {
                $SecretAgency[] = $data[3];
                $SecretMove[] = 1;
                $SecretValue[] += $value;
            } else {
                $position = array_search($data[3], $SecretAgency);
                $SecretMove[$position] += 1;
                $SecretValue[$position] += $value;
            }
        }
        
        if($data[12] == "SAQUE CASH/ATM BB") {
            if(!in_array($data[9], $Drawer)) {
                $Drawer[] = $data[9];
                $DrawerCarrier[] = 1;
                $WithdrawalSum[] = $value;
                $DrawerAgency[] = $data[3];
            } else {
                $position = array_search($data[9], $Drawer);
                $DrawerCarrier[$position] += 1;
                $WithdrawalSum[$position] += $value;
            }
        }
        
        if(($data[12] == "COMPRA A/V - R$ - APRES") ||
            ($data[12] == "COMP A/V-SOL DISP C/CLI-R$ ANT VENC")) {
                if(($data[9] != "NAO SE APLICA") &&
                    ($data[9] != "SEM INFORMACAO") &&
                    ($data[9] != "Sigiloso")) {
                        if(!in_array($data[9], $Purchases)) {
                            $Purchases[] = $data[9];
                            $PurchasesFav[] = 1;
                        } else {
                            $position = array_search($data[9], $Purchases);
                            $PurchasesFav[$position] += 1;
                        }
                    }
            }
            
            $TotalValue += $value;
    }
    fclose($handle);
    
    // Questao K
    echo "> Questao K - ";
    echo "Valor total: R$ " . str_replace(".", ",", (string)$TotalValue) . "\n";
    
    // Questao L
    echo "> Questao L - ";
    echo "Valor total das movimentações sigilosas: R$ " . str_replace(".", ",", (string)$TotalValueSecretive) . "\n";
    
    // Questao M
    echo "> Questao M - ";
    
    $BigMover = array_search(max($SecretMove), $SecretMove);
    
    echo "Maior orgão sigiloso: " .
        $SecretAgency[$BigMover] .
        ". Soma no valor de: R$ " .
        str_replace(".", ",", (string)$SecretValue[$BigMover]) . "\n";
        
        // Questao N
        echo "> Questao N - ";
        
        
        $BigDrawer = array_search(max($DrawerCarrier), $DrawerCarrier);
        
        echo "Portador que mais sacou: " .
            $Drawer[$BigDrawer] .
            ". Valor total dos saques: R$ " .
            str_replace(".", ",", (string)$WithdrawalSum[$BigDrawer]) .
            ". Órgão do portador: " .
            str_replace(".", ",", (string)$DrawerAgency[$BigDrawer]) .
            "\n";
            
            // Questão O
            echo "> Questao O - ";
            
            $MorePurchases = array_search(max($PurchasesFav), $PurchasesFav);
            
            echo "Favorecido com maior numero de compras: " .
                $Purchases[$MorePurchases] .
                ". Compras realizadas: " .
                str_replace(".", ",", (string)$PurchasesFav[$MorePurchases]) . "\n";
}