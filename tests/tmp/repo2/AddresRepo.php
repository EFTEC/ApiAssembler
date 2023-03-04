<?php
/** @noinspection AccessModifierPresentedInspection
 * @noinspection PhpUnusedAliasInspection
 * @noinspection UnknownInspectionInspection
 * @noinspection PhpUnused
 * @noinspection ReturnTypeCanBeDeclaredInspection
 */
namespace eftec\tests\tmp\repo2;

use Exception;

/**
 * Class AddresRepo Copyright (c) Jorge Castro C. (https://github.com/EFTEC/PdoOne)<br>
 * <ul>
 * <li>address_id int </li>
 * <li>address string </li>
 * <li>address2 string </li>
 * <li>district string </li>
 * <li>city_id int </li>
 * <li>postal_code string </li>
 * <li>phone string </li>
 * <li>last_update timestamp </li>
 * <li>_city_id MANYTOONE (CityRepoModel)</li>
 * <li>_customer ONETOMANY (CustomerRepoModel)</li>
 * <li>_staff ONETOMANY (StaffRepoModel)</li>
 * <li>_store ONETOMANY (StoreRepoModel)</li>
 * </ul>
 * Generated by PdoOne Version 2.27 Date generated Mon, 07 Mar 2022 08:48:22 -0400.<br>
 * <b>YOU CAN EDIT THIS CODE</b>. It is not replaced by the generation of the code, unless it is indicated<br>
 * <pre>
 * $code=$pdoOne->generateCodeClassRepo(''address'',''eftec\tests\tmp\repo2'','array('actor'=>'ActorRepo','actor2'=>'Actor2Repo','address'=>'AddresRepo','category'=>'CategoryRepo','city'=>'CityRepo','country'=>'CountryRepo','customer'=>'CustomerRepo','dummyt'=>'DummytRepo','dummytable'=>'DummytableRepo','film'=>'FilmRepo','film2'=>'Film2Repo','film_actor'=>'FilmActorRepo','film_category'=>'FilmCategoryRepo','film_text'=>'FilmTextRepo','fum_jobs'=>'FumJobRepo','fum_logs'=>'FumLogRepo','inventory'=>'InventoryRepo','language'=>'LanguageRepo','mysec_table'=>'MysecTableRepo','payment'=>'PaymentRepo','product'=>'ProductRepo','producttype'=>'ProducttypeRepo','producttype_auto'=>'ProducttypeAutoRepo','rental'=>'RentalRepo','staff'=>'StaffRepo','store'=>'StoreRepo','tablachild'=>'TablachildRepo','tablagrandchild'=>'TablagrandchildRepo','tablaparent'=>'TablaparentRepo','tabletest'=>'TabletestRepo','test_products'=>'TestProductRepo','typetable'=>'TypetableRepo',)','''','array(0=>array(0=>'address_id',1=>'int',2=>NULL,),1=>array(0=>'address',1=>'string',2=>NULL,),2=>array(0=>'address2',1=>'string',2=>NULL,),3=>array(0=>'district',1=>'string',2=>NULL,),4=>array(0=>'city_id',1=>'int',2=>NULL,),5=>array(0=>'postal_code',1=>'string',2=>NULL,),6=>array(0=>'phone',1=>'string',2=>NULL,),7=>array(0=>'last_update',1=>'timestamp',2=>NULL,),8=>array(0=>'_city_id',1=>'MANYTOONE',2=>'CityRepoModel',),9=>array(0=>'_customer',1=>'ONETOMANY',2=>'CustomerRepoModel',),10=>array(0=>'_staff',1=>'ONETOMANY',2=>'StaffRepoModel',),11=>array(0=>'_store',1=>'ONETOMANY',2=>'StoreRepoModel',),)');
 * </pre>
 * @see CityRepoModel
 * @see CustomerRepoModel
 * @see StaffRepoModel
 * @see StoreRepoModel
 */
class AddresRepo extends AbstractAddresRepo
{
    const ME=__CLASS__;
    


}