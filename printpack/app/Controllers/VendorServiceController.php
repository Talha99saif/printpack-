<?php
declare(strict_types=1);
namespace App\Controllers;
final class VendorServiceController {
 public function save(): void {$u=require_role('vendor');$id=(int)($_POST['id']??0);$name=trim($_POST['name']??'');$desc=trim($_POST['description']??'');$cat=(int)($_POST['category_id']??0);$price=max(0,(float)($_POST['base_price']??0));$material=trim($_POST['material']??'');$size=trim($_POST['size_label']??'Custom');$type=trim($_POST['service_type']??'printing');if($name===''||$price<=0){flash('error','Service name and a positive price are required.');redirect('vendor');}if($id){db()->prepare('UPDATE services SET name=?,description=?,category_id=?,base_price=?,material=?,size_label=?,service_type=? WHERE id=? AND vendor_id=?')->execute([$name,$desc,$cat?:null,$price,$material,$size,$type,$id,$u['id']]);}else{db()->prepare('INSERT INTO services(vendor_id,category_id,name,description,base_price,material,size_label,service_type,status) VALUES (?,?,?,?,?,?,?,?,?)')->execute([$u['id'],$cat?:null,$name,$desc,$price,$material,$size,$type,'active']);}flash('success','Service saved.');redirect('vendor');}
}
