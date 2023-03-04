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
 * Class FilmActorRepo Copyright (c) Jorge Castro C. (https://github.com/EFTEC/PdoOne)<br>
 * <ul>
 * <li>actor_id int </li>
 * <li>film_id int </li>
 * <li>last_update timestamp </li>
 * <li>_actor_id ONETOONE (ActorRepoModel)</li>
 * <li>_film_id MANYTOONE (FilmRepoModel)</li>
 * </ul>
 * Generated by PdoOne Version 2.27 Date generated Mon, 07 Mar 2022 08:48:22 -0400.<br>
 * <b>YOU CAN EDIT THIS CODE</b>. It is not replaced by the generation of the code, unless it is indicated<br>
 * <pre>
 * $code=$pdoOne->generateCodeClassRepo(''film_actor'',''eftec\tests\tmp\repo2'','array('actor'=>'ActorRepo','actor2'=>'Actor2Repo','address'=>'AddresRepo','category'=>'CategoryRepo','city'=>'CityRepo','country'=>'CountryRepo','customer'=>'CustomerRepo','dummyt'=>'DummytRepo','dummytable'=>'DummytableRepo','film'=>'FilmRepo','film2'=>'Film2Repo','film_actor'=>'FilmActorRepo','film_category'=>'FilmCategoryRepo','film_text'=>'FilmTextRepo','fum_jobs'=>'FumJobRepo','fum_logs'=>'FumLogRepo','inventory'=>'InventoryRepo','language'=>'LanguageRepo','mysec_table'=>'MysecTableRepo','payment'=>'PaymentRepo','product'=>'ProductRepo','producttype'=>'ProducttypeRepo','producttype_auto'=>'ProducttypeAutoRepo','rental'=>'RentalRepo','staff'=>'StaffRepo','store'=>'StoreRepo','tablachild'=>'TablachildRepo','tablagrandchild'=>'TablagrandchildRepo','tablaparent'=>'TablaparentRepo','tabletest'=>'TabletestRepo','test_products'=>'TestProductRepo','typetable'=>'TypetableRepo',)','''','array(0=>array(0=>'actor_id',1=>'int',2=>NULL,),1=>array(0=>'film_id',1=>'int',2=>NULL,),2=>array(0=>'last_update',1=>'timestamp',2=>NULL,),3=>array(0=>'_actor_id',1=>'ONETOONE',2=>'ActorRepoModel',),4=>array(0=>'_film_id',1=>'MANYTOONE',2=>'FilmRepoModel',),)');
 * </pre>
 * @see ActorRepoModel
 * @see FilmRepoModel
 */
class FilmActorRepo extends AbstractFilmActorRepo
{
    const ME=__CLASS__;
    


}