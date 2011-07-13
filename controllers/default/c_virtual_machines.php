<?php
class Virtual_Machines
{
    private $factory_instance;

    private function get_factory() {
        if ( !isset($this->factory_instance) ) {
            require_once "wrapper/Factory.php";

            $this->factory_instance = new ONAPP_Factory(
                $_SESSION["host"],
                $_SESSION["login"],
                onapp_cryptData($_SESSION["password"], 'decrypt')
            );
       }
       return $this->factory_instance;
   }
   
   /**
    * Main controller function
    *
    * @return void
    */
    public function view()
    {
        $id                 = onapp_get_arg('id');
        $action             = onapp_get_arg('action');
        
        switch($action)
        {
            case 'details':
                $this->show_template_details( $id );
                break;
            case 'create_page':
                $this->show_template_create();
                break;
            case 'startup':
                $this->startup( $id );
                break;
            case 'reboot':
                $this->reboot( $id );
                break;
            case 'shutdown':
                $this->shutdown( $id );
                break;
            case 'suspend':
                $this->suspend( $id );
                break;
            case 'reset_password':
                $this->reset_password( $id );
                break;
            case 'create':
                $this->create();
                break;
            case 'cpu_usage':
                $this->show_template_cpu_usage( $id );
                break;
            case 'delete':
                $this->delete($id);
                break;
            case 'rebuild':
            case 'build':
                $this->build($id);
                break;
            case 'manage_network_interfaces':
                $this->manage_network_interfaces( $id );
                break;
            case 'backup':
                $this->show_template_backup( $id );
                break;
            case 'backup_restore':
                $this->backup_restore( $id );
                break;
            case 'backup_convert':
                $this->backup_convert( $id );
                break;
            case 'backup_delete':
                $this->backup_delete( $id );
                break;
            case 'ip_addresses':
                $this->show_template_ip_addresses( $id );
                break;
            case 'rebuild_network':
                $this->rebuild_network( $id );
                break;
            case 'ip_address_join_new':
                $this->ip_address_join_new( $id );
                break;
            case 'ip_address_delete':
                $this->ip_address_delete( $id );
                break;
            case 'network_interfaces': 
                $this->show_template_network_interfaces( $id );
                break;
            case 'interface_usage': 
                $this->show_template_interface_usage( $id );
                break;
            case 'network_interface_edit': 
                $this->network_interface_edit( $id );
                break;
            case 'network_interface_delete': 
                $this->network_interface_delete( $id );
                break;
            case 'network_interface_create': 
                $this->network_interface_create( $id );
                break;
            case 'disks': 
                $this->show_template_disks( $id );
                break;
            case 'change_owner':
                $this->change_owner( $id );
                break;
            case 'edit':
                $this->edit( $id );
                break;
            case 'autobackup_disable':
                $this->autobackup_disable( $id );
                break;
            case 'autobackup_enable':
                $this->autobackup_enable( $id );
                break;
            case 'disk_edit':
                $this->disk_edit( $id );
                break;
            case 'disk_create':
                $this->disk_create( $id );
                break;
            case 'disk_delete':
                $this->disk_delete( $id );
                break;
            case 'disk_backups':
                $this->show_template_disk_backup( $id );
                break;
            case 'disk_backups_schedule':
                $this->show_template_disk_backups_schedule( $id );
                break;
            case 'disk_backups_schedule_edit':
                $this->disk_backups_schedule_edit( $id );
                break;
            case 'disk_backups_schedule_delete':
                $this->disk_backups_schedule_delete( $id );
                break;
            case 'disk_backups_schedule_create':
                $this->disk_backups_schedule_create( $id );
                break;
            case 'firewall':
                $this->show_template_firewall( $id );
                break;
            case 'firewall_rule_create':
                $this->firewall_rule_create( $id );
                break;
            case 'firewall_rule_move':
                $this->firewall_rule_move( $id );
                break;
            case 'firewall_rule_delete':
                $this->firewall_rule_delete( $id );
                break;
            case 'firewall_rule_edit':
                $this->firewall_rule_edit( $id );
                break;
            case 'firewall_rule_update_defaults':
                $this->firewall_rule_update_defaults( $id );
                break;
            case 'firewall_rules_apply':
                $this->firewall_rules_apply( $id );
                break;
            case 'backup_take':
                $this->backup_take( $id );
                break;
            case 'migrate':
                $this->migrate( $id );
                break;
            case 'edit_admin_note':
                $this->edit_admin_note( $id );
                break;
            default:
                $this->show_template_view();
                break;
        }
    }

   /**
    * Displays default page with virtual machine list
    *
    * @param string error message
    * @param other message
    * @return void
    */
    private function show_template_view($error = NULL, $message = NULL)
    {
        $onapp = $this->get_factory();                                                                        // print_r($onapp);die();
        
        $hypervisor_id    = onapp_get_arg('hypervisor_id');
        $hypervisor_label = onapp_get_arg('hypervisor_label');
        
        onapp_permission(array('virtual_machines', 'virtual_machines.read.own', 'virtual_machines.read'));

        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);              
        $virtual_machines_array = $virtual_machine->getList();                                                 // print('<pre>'); print_r($virtual_machines_array);die();

        $virtual_machines = $virtual_machines_array;
            
        $vm_backup = $onapp->factory('VirtualMachine_Backup', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        
        if( is_array($virtual_machines) && $virtual_machines[0]->_id != '')
        {    
            foreach($virtual_machines as $virtual_machine)
            { 
                $vm_backup_obj = $vm_backup->getList($virtual_machine->_id);
                $size_and_quantity = $this->calculateBackups($vm_backup_obj);
                $vm_backups [] = $size_and_quantity;
            }
        }
        else if( ! is_array($virtual_machines) && $virtual_machines->_id != '')
        {  
            $vm_backup_obj = $vm_backup->getList($virtual_machines->_id);
            $size_and_quantity = $this->calculateBackups($vm_backup_obj);
            $vm_backups = $size_and_quantity; 
        }    

        if( ! is_null($hypervisor_id) && is_array($virtual_machines))
        {
            foreach($virtual_machines as $virtual_machine)
                if($virtual_machine->_hypervisor_id == $hypervisor_id)
                    $hypervisor_vms[] = $virtual_machine;
            $virtual_machines = $hypervisor_vms;
        }
        elseif(! is_null($hypervisor_id) && ! is_array($virtual_machines))
            if($virtual_machines->_hypervisor_id == $hypervisor_id)
                $hypervisor_vms = $virtual_machines;
        
       if( is_null($virtual_machines->_id) && ! is_array( $virtual_machines ) )
            $virtual_machines = NULL;
             
       $params = array(
           'hypervisor_id'     =>    $hypervisor_id,
           'vm_backups'        =>    $vm_backups,
           'virtual_machines'  =>    $virtual_machines,
           'hypervisor_label'  =>    $hypervisor_label,
           'title'             =>    onapp_string('VIRTUAL_MACHINES' ),
           'info_title'        =>    onapp_string('VIRTUAL_MACHINES'),
           'info_body'         =>    onapp_string('VIRTUAL_MACHINE_INFO'),
           'error'             =>    onapp_string( $error ),
           'message'           =>    onapp_string( $message )
       );  
                                                                           
       onapp_show_template( 'vm_view', $params );                                      
     }
  
  /**
   * Displays particular virtual machine details page
   *
   * @param integer virtual Machine id
   * @param string error message
   * @param string other message
   * @return void
   */
    public function show_template_details($id, $error = NULL, $message = NULL)
    {
        $onapp = $this->get_factory();

        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $vm_obj = $virtual_machine->load($id);

        $hypervisor = $onapp->factory('Hypervisor', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $hypervisor_obj = $hypervisor->load($vm_obj->_hypervisor_id);

        $vm_backup = $onapp->factory('VirtualMachine_Backup', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $vm_backup_obj = $vm_backup->getList($id);
        
        $user = $onapp->factory('User', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $user_obj = $user->load($vm_obj->_user_id); 

        $size_and_quantity = $this->calculateBackups($vm_backup_obj);

        $params = array(
            'virtual_machine_id'  =>  $id,
            'user_obj'            =>  $user_obj,
            'backups_quantity'    =>  $size_and_quantity['quantity'],
            'backups_total_size'  =>  $size_and_quantity['size'],
            'profile_obj'         =>  $_SESSION['profile_obj'],
            'vm_backup_obj'       =>  $vm_backup_obj,
            'hypervisor_obj'      =>  $hypervisor_obj,
            'vm_obj'              =>  $vm_obj,
            'title'               =>  onapp_string('VIRTUAL_MACHINE_DETAILS'),
            'info_title'          =>  onapp_string('VIRTUAL_MACHINE_DETAILS'),
            'info_body'           =>  onapp_string('VIRTUAL_MACHINE_DETAILS_INFO'),
            'error'               =>  onapp_string( $error ),
            'message'             =>  onapp_string( $message ),
        ); 
        onapp_show_template( 'vm_details', $params );
    }

    /**
     * Shows creating a new virtual machine page
     *
     * @param error message
     * @param other message
     * @return void
     */
     private function show_template_create($error = NULL, $message = NULL)
     {
        $onapp = $this->get_factory();

        $hypervisor = $onapp->factory('Hypervisor', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $hypervisor_obj = $hypervisor->getList();

        $vm = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $vm_obj = $vm->getList();

        $templates = $onapp->factory('Template', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $templates_obj = $templates->getList();

        $data_store_zone = $onapp->factory('DataStoreZone', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $data_store_zone_obj = $data_store_zone->getList();

        $network_zone = $onapp->factory('NetworkZone', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $network_zone_obj = $network_zone->getList();

        $hypervisor_zones = $onapp->factory('HypervisorZone', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $hypervisor_zones_obj = $hypervisor_zones->getList();

        $params = array(
            'network_zone_obj'      =>     $network_zone_obj,
            'data_store_zone_obj'   =>     $data_store_zone_obj,
            'hypervisor_zones_obj'  =>     $hypervisor_zones_obj,
            'templates_obj'         =>     $templates_obj,
            'hypervisor_obj'        =>     $hypervisor_obj,
            'vm_obj'                =>     $vm,
            'title'                 =>     onapp_string('CREATE_VIRTUAL_MACHINE'),
            'info_title'            =>     onapp_string('CREATE_VIRTUAL_MACHINE'),
            'info_body'             =>     onapp_string('CREATE_VIRTUAL_MACHINE_INFO'),
            'error'                 =>     onapp_string( $error ),
            'message'               =>     onapp_string( $message ),
        );
        onapp_show_template( 'vm_create', $params );
     }
    
    /**
     * Shows virtual machine cpu usage
     *
     * @param integer virtual machine id
     * @return void
     */
     private function show_template_cpu_usage($id)
     {
        $onapp = $this->get_factory();

        onapp_permission(array('virtual_machines', 'virtual_machines.power', 'virtual_machines.power.own'));
        
        $cpuusage = $onapp->factory('VirtualMachine_CpuUsage', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $cpuusage->_virtual_machine_id = $id;
        $list = $cpuusage->getList();

        $xaxis = '';
        $yaxis = '';
    
        $date = array();
        for ($i = 0; $i < count($list); $i++) {
            if (isset($date[$list[$i]->_created_at]))
                $date[$list[$i]->_created_at]++;
            else
                $date[$list[$i]->_created_at] = 1;
        }
    
        for ($i = 0; $i < count($list); $i++) {
            $created_at = str_replace(array('T', 'Z'), ' ', $list[$i]->_stat_time);
            $xaxis .= "<value xid='$i'>".$created_at."</value>";
            $xaxis .= "<value xid='$i'>".$created_at."</value>";
            if ( $date[$list[$i]->_created_at] * 100 != 0 ){
                $usage = $list[$i]->_cpu_time / ($date[$list[$i]->_created_at] * 10) / 100 ;
                }
            else
                $usage = 0;
            $yaxis .= "<value xid='$i'>".number_format($usage, 2)."</value>";
        }
        $params = array(
            'virtual_machine_id'  =>  $id,
            'xaxis'               => $xaxis,
            'yaxis'               => $yaxis,
            'title'               => onapp_string ('CPU_USAGE'),
            'info_title'          => onapp_string( 'CPU_USAGE_FOR_THIS_VM' ),
            'info_body'           => onapp_string( 'CPU_USAGE_FOR_THIS_VM_INFO' ),
        ); 
        onapp_show_template( 'vm_cpuUsage', $params );  
     }
     
    /**
     * Shows virtual machine backups
     *
     * @param integer virtual machine id
     * @return void
     */
     private function show_template_backup($id, $error =  NULL, $message = NULL)
     {
        onapp_permission(array('backups.read.own', 'backups.read', 'backups'));

        $onapp = $this->get_factory();

        $backup = $onapp->factory('VirtualMachine_Backup', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $backup->virtual_machine_id = $id;
        $backup_obj = $backup->getList($id);                                       
        
        if( is_null($backup_obj->_id) && ! is_array( $backup_obj ) )
            $backup_obj = NULL;
                   
        $params = array(
            'virtual_machine_id'  =>     $id,
            'backup_obj'          =>     $backup_obj,
            'title'               =>     onapp_string('BACKUPS_FOR_THIS_VM'),
            'info_title'          =>     onapp_string('BACKUPS_FOR_THIS_VM'),
            'info_body'           =>     onapp_string('BACKUPS_FOR_THIS_VM_INFO'),
            'error'               =>     onapp_string( $error ),
            'message'             =>     onapp_string( $message ),
        );

        onapp_show_template( 'vm_backup', $params );  
     }
     
    /**
     * Shows virtual machine firewall
     *
     * @param integer virtual machine id
     * @return void
     */
     private function show_template_firewall( $id )
     {
        onapp_permission(array('firewall_rules.read.own', 'firewall_rules.read', 'firewall_rules'));

        $onapp = $this->get_factory();

        $firewall = $onapp->factory('VirtualMachine_FirewallRule', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $firewall->_virtual_machine_id = $id;
        $firewall_obj = $firewall->getList();                                                           // print('<pre>'); print_r($firewall_obj); print('</pre>'); die();

        $network_interface = $onapp->factory('VirtualMachine_NetworkInterface', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $network_interface_obj = $network_interface->getList( $id );

        foreach($network_interface_obj as $network_interface)
            $network_interface_object[$network_interface->_id] = $network_interface;                //    print('<pre>'); print_r($network_interface_obj); print('</pre>'); die();

        foreach($firewall_obj as $firewall)
            $firewall_by_network[$firewall->_network_interface_id][] = $firewall;                      //print('<pre>'); print_r($firewall_by_network); print('</pre>'); die();
            
        if( is_null($firewall_obj->_id) && ! is_array( $firewall_obj ) )
            $firewall_obj = NULL;
                   
        $params = array(
            'firewall_by_network'    =>     $firewall_by_network,
            'commands'               =>     array('ACCEPT', 'DROP'),
            'virtual_machine_id'     =>     $id,
            'network_interface_obj'  =>     $network_interface_object,
            'firewall_obj'           =>     $firewall_obj,
            'title'                  =>     onapp_string('FIREWALL_RULES'),
            'info_title'             =>     onapp_string('FIREWALL_RULES'),
            'info_body'              =>     onapp_string('FIREWALL_RULES_INFO'),
            'error'                  =>     onapp_string( $error ),
            'message'                =>     onapp_string( $message ),
        );

        onapp_show_template( 'vm_firewall', $params );
     }

    /**
     * Shows virtual machine disk backups schedules
     *
     * @param integer virtual machine disk id
     * @return void
     */
     private function show_template_disk_backups_schedule( $id, $error )
     {
        onapp_permission(array('schedules', 'schedules.read', 'schedules.read.own'));

        $onapp = $this->get_factory();
        $schedule = $onapp->factory('Disk_Schedule', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $schedule_obj = $schedule->getListByDiskId( $id );                                          //print('<pre>'); print_r($schedule_obj); print('</pre>'); die();

        if( is_null($schedule_obj->_id) && ! is_array( $schedule_obj ) )
            $schedule_obj = NULL;
                                                                                      //print('<pre>'); print_r($schedule_obj); print('</pre>'); die();
        $params = array(
            'schedule_obj'    =>     $schedule_obj,
            'disk_id'         =>     $id,
            'title'           =>     onapp_string('SCHEDULES_'),
            'info_title'      =>     onapp_string('SCHEDULES_'),
            'info_body'       =>     onapp_string('SCHEDULES_INFO'),
            'error'           =>     $error,
            'message'         =>     onapp_string( $message ),

        );

        onapp_show_template( 'vm_diskBackupsSchedule', $params );
     }

    /**
     * Shows disk backups list
     *
     * @param integer virtual machine disk id
     * @return void
     */
     private function show_template_disk_backup( $id )
     {
        $onapp = $this->get_factory();

        onapp_permission(array('backups.read.own', 'backups.read', 'backups'));

        $backup = $onapp->factory('VirtualMachine_Backup', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $backup_obj = $backup->diskBackups($id);                                                  // print('<pre>'); print_r($backup_obj); print('</pre>'); die();

        if( is_null($backup_obj->_id) && ! is_array( $backup_obj ) )
            $backup_obj = NULL;

        $params = array(
            'disk_id'              =>     $id,
            'virtual_machine_id'   =>     onapp_get_arg('virtual_machine_id'),
            'backup_obj'           =>     $backup_obj,
            'title'                =>     onapp_string('BACKUPS_FOR_THIS_DISK'),
            'info_title'           =>     onapp_string('BACKUPS_FOR_THIS_DISK'),
            'info_body'            =>     onapp_string('BACKUPS_FOR_THIS_DISK_INFO'),
            'error'                =>     onapp_string( $error ),
            'message'              =>     onapp_string( $message ),

        );

        onapp_show_template( 'vm_diskBackup', $params );
     }

     /**
     * Shows change virtual machine page
     *
     * @param integer virtual machine id
     * @return void
     */
     private function show_template_change_owner( $id )
     {
        $onapp = $this->get_factory();

        onapp_permission(array('virtual_machines', 'virtual_machines.change_owner'));

        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $vm_obj = $virtual_machine->load($id);                                                              //  print('<pre>'); print_r($vm_obj); print('</pre>'); die();

        $user = $onapp->factory('User', ONAPP_WRAPPER_LOG_REPORT_ENABLE); 
        $user_obj = $user->getList( );                                                                      // print('<pre>'); print_r($user_obj); print('</pre>'); die();

        if( is_null($user_obj[0]->_id) )
            $user_obj = NULL;

        $params = array(
            'vm_obj'          =>     $vm_obj,
            'user_obj'        =>     $user_obj,
            'title'           =>     onapp_string('CHANGE_THIS_VIRTUAL_MACHINE_OWNER'),
            'info_title'      =>     onapp_string('CHANGE_THIS_VIRTUAL_MACHINE_OWNER'),
            'info_body'       =>     onapp_string('CHANGE_THIS_VIRTUAL_MACHINE_OWNER_INFO'),

        );
        
        onapp_show_template( 'vm_changeOwner', $params );
     }
     
    /**
     * Shows virtual machine disks list
     *
     * @param integer virtual machine id
     * @return void
     */
     private function show_template_disks($id, $error =  NULL, $message = NULL)
     {
        $onapp = $this->get_factory();

        onapp_permission(array('disks', 'disks.read', 'disks.read.own'));

        $disk = $onapp->factory('Disk', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $disk->virtual_machine_id = $id;
        $disk_obj = $disk->getList($id);                                                                // print('<pre>'); print_r($disk_obj); print('</pre>'); die();
        
        $data_store = $onapp->factory('DataStore', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $data_store_obj = $data_store->getList(); 
        
        foreach($data_store_obj as $data_store)
            $data_store_object[$data_store->_id] = $data_store;                                          //print('<pre>'); print_r($data_store_object); print('</pre>'); die();
        
        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $vm_obj = $virtual_machine->getList();      
        
        foreach($vm_obj as $vm)
            $vm_object[$vm->_id] = $vm; 
                                                                                                              //print('<pre>'); print_r($vm_object); print('</pre>'); die();
        $backup = $onapp->factory('VirtualMachine_Backup', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
       // $backup_obj = $backup->diskBackups();
                                                                                                  // print('<pre>'); print_r($backup_obj); print('</pre>'); die();
        foreach($disk_obj as $disk)
        {
            $backup_obj = $backup->diskBackups($disk->_id);
            if(! is_null ( $backup_obj->_id ) || is_array( $backup_obj ) )
                $backup_object[$disk->_id] = count($backup_obj);
            else
                $backup_object[$disk->_id] = 0;   
        }
                                                                                                         // print('<pre>'); print_r($backup_object); print('</pre>'); die();   
        if( is_null($disk_obj[0]->_id) )
            $disk_obj = NULL;
                                                                                                         // print('<pre>'); print_r($disk_obj); print('</pre>'); die();
        $params = array(
            'backup_quantity'   =>   $backup_object,
            'vm_obj'            =>   $vm_object,
            'data_store_obj'    =>   $data_store_object,
            'disk_obj'          =>   $disk_obj,
            'title'             =>   onapp_string('DISK_SETTINGS'),
            'info_title'        =>   onapp_string('DISK_SETTINGS'),
            'info_body'         =>   onapp_string('DISK_SETTINGS_INFO'),
            'error'             =>   onapp_string( $error ),
            'message'           =>   onapp_string( $message ),

        );

        onapp_show_template( 'vm_disk', $params );  
     }
   
     /**
     * Shows virtual machine ip addresses list
     *
     * @param integer virtual machine id
     * @return void
     */
     private function show_template_ip_addresses($id, $error =  NULL, $message = NULL)
     {  
        onapp_permission(array('ip_address_joins', 'ip_address_joins.read', 'ip_address_joins.read.own'));

        $onapp = $this->get_factory();

        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $vm_obj = $virtual_machine->load($id);                                                            

        $ip_address = $onapp->factory('VirtualMachine_IpAddressJoin', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $ip_address_obj = $ip_address->getList($id);

        $network_interface = $onapp->factory('VirtualMachine_NetworkInterface', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $network_interface_obj = $network_interface->getList($id);                                                     //  print_r($network_interface_obj); die();

        foreach( $network_interface_obj as $network_interface )
            $network_interface_array[$network_interface->_id] = $network_interface;
        
        $network = $onapp->factory('Network', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $network_obj = $network->getList();                                                                         //  print('<pre>');print_r($network_obj);print('</pre>');

        $hypervisor = $onapp->factory('Hypervisor', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $hypervisor_obj = $hypervisor->load($vm_obj->_hypervisor_id);                             //echo   $vm_obj->_hypervisor_id; die();             print_r($hypervisor);    //print_r($hypervisor_obj->_label); die();

        foreach($network_obj as $network)
            $network_array[$network->_id] = $network;
        if( $ip_address_obj->_id == NULL && ! is_array( $ip_address_obj) )
                $ip_address_obj = NULL;
                                                                                                                                 //print('<pre>');print_r($network_array);print('</pre>'); die();
        $params = array(
            'virtual_machine_id'    =>     $id,
            'hypervisor_label'      =>     $hypervisor_obj->_label,
            'network_obj'           =>     $network_array,
            'ip_address_obj'        =>     $ip_address_obj,
            'network_interface_obj' =>     $network_interface_array,
            'title'                 =>     onapp_string('IP_ADDRESSES_FOR_THIS_VIRTUAL_MACHINE'),
            'info_title'            =>     onapp_string('IP_ADDRESSES_FOR_THIS_VIRTUAL_MACHINE'),
            'info_body'             =>     onapp_string('IP_ADDRESSES_FOR_THIS_VIRTUAL_MACHINE_INFO'),
            'error'                 =>     onapp_string( $error ),
            'message'               =>     onapp_string( $message ),
        );
                                                                                                                                  //   print_r($network_interface_array);die();
        onapp_show_template( 'vm_ipAddress', $params );           //print('<pre>');  print_r($network_interface_obj); print('</pre>'); //  print('<pre>');  print_r($ip_address_obj); print('</pre>');
     }

    /**
     * Shows virtual machine backups
     *
     * @param integer virtual machine id
     * @return void
     */
     private function show_template_backup_convert( $id )
     {
        onapp_permission(array('backups.convert.own', 'backups.convert', 'backups'));

        $params = array(
            'id'              =>     $id,
            'title'           =>     onapp_string('CONVERT_THIS_BACKUP_TO_TEMPLATE'),
            'info_title'      =>     onapp_string('CONVERT_THIS_BACKUP_TO_TEMPLATE'),
            'info_body'       =>     onapp_string('CONVERT_THIS_BACKUP_TO_TEMPLATE_INFO'),
            
        );

        onapp_show_template( 'vm_backupConvert', $params );
     }
     
    /**
     * Shows administrator's note edit page
     *
     * @param integer virtual machine id
     * @return void
     */
     private function show_template_edit_admin_note( $id )
     {
        onapp_permission('virtual_machines', 'sysadmin_tools');

        $onapp = $this->get_factory();

        $vm = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $vm_obj = $vm->load($id);                                                    // print('<pre>'); print_r($vm_obj); print('</pre>');die();


        $params = array(
            'current_admin_note'  =>     $vm_obj->_admin_note,
            'id'                  =>     $id,
            'title'               =>     onapp_string('EDIT_ADMIN_NOTE'),
            'info_title'          =>     onapp_string('EDIT_ADMIN_NOTE'),
            'info_body'           =>     onapp_string('EDIT_ADMIN_NOTE_INFO'),
            
        );

        onapp_show_template( 'vm_editAdminNote', $params );
     }


     
    /**
     * Shows virtual machine disk edit page
     *
     * @param integer virtual machine disk id
     * @return void
     */
     private function show_template_disk_edit( $id )
     {
        onapp_permission(array('disks', 'disks.update', 'disks.update.own'));

        $onapp = $this->get_factory();

        $disk = $onapp->factory('Disk', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $disk_obj = $disk->load($id);                                              //print('<pre>'); print_r($disk_obj); print('</pre>');die();

        $params = array(
            'disk_obj'        =>     $disk_obj,
            'title'           =>     onapp_string('EDIT_DISK'),
            'info_title'      =>     onapp_string('EDIT_DISK'),
            'info_body'       =>     onapp_string('EDIT_DISK_INFO'),
            
        );

        onapp_show_template( 'vm_diskEdit', $params );
     }

     /**
     * Shows virtual machine disk edit page
     *
     * @param integer virtual machine disk id
     * @return void
     */
     private function show_template_migrate($id)
     { 
        onapp_permission(array('virtual_machines', 'virtual_machines.migrate.own', 'virtual_machines.migrate'));

        $onapp = $this->get_factory();

        $hypervisors = $onapp->factory('Hypervisor', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $hypervisors_obj = $hypervisors->getList( );                                            //  print('<pre>'); print_r($hypervisor_obj); print('</pre>');die();

        $vm = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $vm_obj = $vm->load($id);                                                                     //print('<pre>'); print_r($vm_obj); print('</pre>');die();
        
        $hypervisor = $onapp->factory('Hypervisor', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $hypervisor_obj = $hypervisor->load($vm_obj->_hypervisor_id);                                            //  print('<pre>'); print_r($hypervisor_obj); print('</pre>');die();

        $params = array(
            'virtual_machine_id' =>     $id,
            'hypervisor_obj'     =>     $hypervisor_obj,
            'hypervisors_obj'    =>     $hypervisors_obj,
            'title'              =>     onapp_string('MIGRATE_VIRTUAL_MACHINE'),
            'info_title'         =>     onapp_string('MIGRATE_VIRTUAL_MACHINE'),
            'info_body'          =>     onapp_string('MIGRATE_VIRTUAL_MACHINE_INFO'),

        );

        onapp_show_template( 'vm_migrate', $params );
     }

    /**
     * Shows disk backups schedule edit page
     *
     * @param integer disk backup schedule id
     * @return void
     */
     private function show_template_disk_backups_schedule_edit( $id )
     {
        onapp_permission(array('schedules', 'schedules.update'));

        $onapp = $this->get_factory();

        $schedule = $onapp->factory('Disk_Schedule', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $schedule_obj = $schedule->load( $id );                                              //print('<pre>'); print_r($schedule_obj); print('</pre>');die();

        $periods = array(
                'days'    =>    'Days',
                'weeks'   =>    'Weeks',
                'months'  =>    'Months',
                'years'   =>    'Years'
            );
        $params = array(
            'periods'         =>     $periods,
            'schedule_obj'    =>     $schedule_obj,
            'title'           =>     onapp_string('EDIT_SCHEDULE'),
            'info_title'      =>     onapp_string('EDIT_SCHEDULE'),
            'info_body'       =>     onapp_string('EDIT_SCHEDULE_INFO'),

        );

        onapp_show_template( 'vm_diskBackupsScheduleEdit', $params );
     }
     
    /**
     * Shows firewall rule edit page
     *
     * @param integer firewall id
     * @return void
     */
     private function show_template_firewall_rule_edit( $id )
     {
        onapp_permission(array('firewall_rules', 'firewall_rules.update'));

        $onapp = $this->get_factory();
        
        $virtual_machine_id = onapp_get_arg('virtual_machine_id');
        
        $firewall = $onapp->factory('VirtualMachine_FirewallRule', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $firewall->_virtual_machine_id = $virtual_machine_id;
        $firewall_obj = $firewall->load( $id );                                             // print('<pre>'); print_r($firewall_obj); print('</pre>');die();

        $network_interface = $onapp->factory('VirtualMachine_NetworkInterface', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $network_interface_obj = $network_interface->getList( $virtual_machine_id );
                                                                                         // print('<pre>'); print_r($network_interface_obj); print('</pre>'); die();        
        $params = array(
            'virtual_machine_id'     =>     $virtual_machine_id,
            'network_interface_obj'  =>     $network_interface_obj,
            'protocols'              =>     array('TCP', 'UDP'),
            'commands'               =>     array('ACCEPT', 'DROP'),
            'firewall_obj'           =>     $firewall_obj,
            'title'                  =>     onapp_string('UPDATE_FIREWALL_RULE'),
            'info_title'             =>     onapp_string('UPDATE_FIREWALL_RULE'),
            'info_body'              =>     onapp_string('UPDATE_FIREWALL_RULE_INFO'),

        );

        onapp_show_template( 'vm_firewallRuleEdit', $params );
     }
     
    /**
     * Shows disk backups schedule create page
     *
     * @param integer disk backup schedule id
     * @return void
     */
     private function show_template_disk_backups_schedule_create( $id )
     {
        onapp_permission(array('schedules', 'schedules.create'));

        $onapp = $this->get_factory();

        $params = array(
            'disk_id'         =>     $id,
            'title'           =>     onapp_string('ADD_A_SCHEDULE_FOR_THIS_DISK'),
            'info_title'      =>     onapp_string('ADD_A_SCHEDULE_FOR_THIS_DISK'),
            'info_body'       =>     onapp_string('ADD_A_SCHEDULE_FOR_THIS_DISK_INFO'),
        );

        onapp_show_template( 'vm_diskBackupsScheduleCreate', $params );
     }
     
    /**
     * Shows virtual machine disk create page
     *
     * @param integer virtual machine id
     * @return void
     */
     private function show_template_disk_create( $id )
     { 
        onapp_permission(array('disks', 'disks.create'));

        $onapp = $this->get_factory();

        $data_store = $onapp->factory('DataStore', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $data_store_obj = $data_store->getList( );                                             // print('<pre>'); print_r($data_store_obj); print('</pre>');die();

        $params = array(
            'data_store_obj'      =>   $data_store_obj,
            'virtual_machine_id'  =>   $id,
            'title'               =>   onapp_string('ADD_NEW_DISK'),
            'info_title'          =>   onapp_string('ADD_NEW_DISK'),
            'info_body'           =>   onapp_string('ADD_NEW_DISK_INFO'),

        );

        onapp_show_template( 'vm_diskCreate', $params );
     }
     
    /**
     * Shows 'Allocate New IP Address from Global Pool' page
     *
     * @param integer virtual machine id
     * @return void
     */
     private function show_template_ip_address_join_new($id)
     { 
        onapp_permission(array('ip_address_joins', 'ip_address_joins.create', 'ip_address_joins.create.own'));
        
        $onapp = $this->get_factory();
        
        $vm = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $vm_obj = $vm->load($id);                                                                     //print('<pre>'); print_r($vm_obj); print('</pre>');die();
        
        $hypervisor = $onapp->factory('Hypervisor', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $hypervisor_obj = $hypervisor->load($vm_obj->_hypervisor_id);
        
        $network_join = $onapp->factory('Hypervisor_NetworkJoin', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $network_join_obj = $network_join->getList($vm_obj->_hypervisor_id);                           // print('<pre>'); print_r($network_join_obj); print('</pre>');die();
        
        $network = $onapp->factory('Network', ONAPP_WRAPPER_LOG_REPORT_ENABLE);                              
        foreach($network_join_obj as $network_join){
            $network_obj = $network->load($network_join->_network_id);
            $network_obj_array[$network_join->_id] = $network_obj; 
        }                                                                                              //  print('<pre>'); print_r($network_obj_array); print('</pre>');die();
        
        $network_interface = $onapp->factory('VirtualMachine_NetworkInterface', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $network_interface_obj = $network_interface->getList($id);                                     //print('<pre>'); print_r($network_interface_obj); print('</pre>');die();
        foreach($network_interface_obj as $network_interface_item){
            $network_interface_object[$network_interface_item->_network_join_id] = $network_interface_item; } // print('<pre>'); print_r($network_interface_object); print('</pre>');die();
        
        $ip_addresses = $onapp->factory('IpAddress', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        
        foreach($network_obj_array as $interface_id => $network){
            $ip_addresses_object = $ip_addresses->getList($network->_id);                                // print('<pre>'); print_r($ip_addresses_object); print('</pre>');
            foreach($ip_addresses_object as $object){
                if( $object->_free == 1)
                    $ip_addresses_array[$network_interface_object[$interface_id]->_id][$object->_id] = $object->_address. '/' . $object->_netmask . '/' .$object->_gateway ;
            }
        }                                                                                                 //print('<pre>'); print_r($ip_addresses_array); print('</pre>');die();
                                                                                                          
        $params = array(
            'ip_addresses'           =>     json_encode( $ip_addresses_array ),
            'network_interface_obj'  =>     $network_interface_obj,
            'id'                     =>     $id,
            'title'                  =>     onapp_string('ALLOCATE_NEW_IP_ADDRESS_ASSIGNMENT'),
            'info_title'             =>     onapp_string('ALLOCATE_NEW_IP_ADDRESS_ASSIGNMENT'),
            'info_body'              =>     onapp_string('ALLOCATE_NEW_IP_ADDRESS_ASSIGNMENT_INFO'),

        );

        onapp_show_template( 'vm_IpAddressJoinNew', $params );
     }

    /**
     * Shows network interface list for virtual machine
     *
     * @param integer virtual machine id
     * @return void
     */
     private function show_template_network_interfaces( $id, $error =  NULL )
     {
        onapp_permission(array('networks.read', 'networks', 'virtual_machines'));

        $onapp = $this->get_factory();

        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $vm_obj = $virtual_machine->load($id);                                                                      //  print('<pre>'); print_r($vm_obj); print('</pre>');die();

        $ip_address = $onapp->factory('VirtualMachine_IpAddressJoin', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $ip_address_obj = $ip_address->getList($id);

        $network_interface = $onapp->factory('VirtualMachine_NetworkInterface', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $network_interface_obj = $network_interface->getList($id);                                                  //print('<pre>'); print_r($network_interface_obj); print('</pre>');die();

        foreach( $network_interface_obj as $network_interface )
            $network_interface_array[$network_interface->_id] = $network_interface;        
                                                                                 // print('<pre>');print_r($network_obj);print('</pre>'); die();
        $network_join = $onapp->factory('Hypervisor_NetworkJoin', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $network_join_obj = $network_join->getList($vm_obj->_hypervisor_id);  
        
        $hypervisor = $onapp->factory('Hypervisor', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $hypervisor_obj = $hypervisor->load($vm_obj->_hypervisor_id);                                             //echo   $vm_obj->_hypervisor_id; die();    
                                                                                                                   // print('<pre>');print_r($network_join_obj);print('</pre>'); die();
        foreach($network_join_obj as $network_join)
            $network_join_array[$network_join->_id] = $network_join;
         
        $network = $onapp->factory('Network', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
         
        foreach($network_join_array as $network_join){
            $network_obj = $network->load($network_join->_network_id);
            $network_obj_array[$network_join->_id] = $network_obj; 
        }                                                                                                  // print('<pre>');print_r($network_obj_array);print('</pre>'); die();
                                                                                               
        if( is_null($network_interface_obj->_id) && ! is_array($network_interface_obj) )
            $network_interface_obj = NULL;

        $params = array(
            'virtual_machine_id'    =>     $vm_obj->_id,
            'hypervisor_label'      =>     $hypervisor_obj->_label,
            'network_obj'           =>     $network_join_array,
            'network_labels'        =>     $network_obj_array,
            'network_interface_obj' =>     $network_interface_obj,
            'id'                    =>     $id,
            'title'                 =>     onapp_string('NETWORK_INTERFACE_FOR_THIS_VIRTUAL_MACHINE'),
            'info_title'            =>     onapp_string('NETWORK_INTERFACE_FOR_THIS_VIRTUAL_MACHINE'),
            'info_body'             =>     onapp_string('NETWORK_INTERFACE_FOR_THIS_VIRTUAL_MACHINE_INFO'),
        );                                                                                                   //print('<pre>');print_r($network_array);print('</pre>'); die();

        onapp_show_template( 'vm_networkInterface', $params );                                              // print('<pre>');  print_r($ip_address_obj); print('</pre>');
     }
    
    /**
     * Shows 'Edit network interface' page
     *
     * @param integer network interface id
     * @return void
     */
     private function show_template_network_interface_edit( $id )
     { 
        onapp_permission(array('networks.update', 'networks'));
        
        $virtual_machine_id = onapp_get_arg('virtual_machine_id');
        
        $onapp = $this->get_factory();
        
        $network_interface = $onapp->factory('VirtualMachine_NetworkInterface', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $network_interface_obj = $network_interface->load($id, $virtual_machine_id);                                                         //  print('<pre>'); print_r($network_interface_obj); print('</pre>');die();        
        
        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $vm_obj = $virtual_machine->load( $virtual_machine_id );
        
        $hypervisor = $onapp->factory('Hypervisor', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $hypervisor_obj = $hypervisor->load($vm_obj->_hypervisor_id);
        
        $network_join = $onapp->factory('Hypervisor_NetworkJoin', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $network_join_obj = $network_join->getList($vm_obj->_hypervisor_id);                               // print('<pre>'); print_r($network_join_obj); print('</pre>');die();
        
        $network = $onapp->factory('Network', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
         
        foreach($network_join_obj as $network_join){
            $network_obj = $network->load($network_join->_network_id);
            $network_obj_array[$network_join->_id] = $network_obj; 
        }                                                                                             //  print('<pre>'); print_r($network_obj_array); print('</pre>');die();
        
        $params = array(
            'hypervisor_label'      =>     $hypervisor_obj->_label,
            'network_interface_obj' =>     $network_interface_obj,
            'network_obj'           =>     $network_obj_array,
            'id'                    =>     $id,
            'title'                 =>     onapp_string('EDIT_NETWORK_INTERFACE'),
            'info_title'            =>     onapp_string('EDIT_NETWORK_INTERFACE'),
            'info_body'             =>     onapp_string('EDIT_NETWORK_INTERFACE_INFO'),

        );

        onapp_show_template( 'vm_networkInterfaceEdit', $params );  
    }
    
    /**
     * Shows 'Create network interface' page
     *
     * @param integer virtual machine id
     * @return void
     */
     private function show_template_network_interface_create( $id )
     {  
        $onapp = $this->get_factory();                                                       
        
        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $vm_obj = $virtual_machine->load( $id );
        
        $hypervisor = $onapp->factory('Hypervisor', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $hypervisor_obj = $hypervisor->load($vm_obj->_hypervisor_id);
        
        $network_join = $onapp->factory('Hypervisor_NetworkJoin', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $network_join_obj = $network_join->getList($vm_obj->_hypervisor_id);                               // print('<pre>'); print_r($network_join_obj); print('</pre>');die();
        
        $network = $onapp->factory('Network', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
         
        foreach($network_join_obj as $network_join){
            $network_obj = $network->load($network_join->_network_id);
            $network_obj_array[$network_join->_id] = $network_obj; 
        }                                                                                             //  print('<pre>'); print_r($network_obj_array); print('</pre>');die();                                                                  //  print('<pre>'); print_r($network_obj); print('</pre>');die();
        
        $params = array(
            'hypervisor_label'      =>     $hypervisor_obj->_label,
            'network_obj'           =>     $network_obj_array,
            'virtual_machine_id'    =>     $id,
            'title'                 =>     onapp_string('ADD_NEW_NETWORK_INTERFACE'),
            'info_title'            =>     onapp_string('ADD_NEW_NETWORK_INTERFACE'),
            'info_body'             =>     onapp_string('ADD_NEW_NETWORK_INTERFACE_INFO'),

        );

        onapp_show_template( 'vm_networkInterfaceCreate', $params );
     }

    /**
     * Shows virtual machine resource allocation page
     *
     * @param integer virtual machine id
     * @return void
     */
     private function show_template_edit( $id )
     {
        onapp_permission(array('virtual_machines', 'virtual_machines.update.own', 'virtual_machines.update'));

        $onapp = $this->get_factory();

        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $vm_obj = $virtual_machine->load($id);                                                                  //   print('<pre>'); print_r($vm_obj); print('</pre>');die();

        $params = array(
            'vm_obj'                =>     $vm_obj,
            'title'                 =>     onapp_string('ADJUST_RESOURCE_ALLOCATIONS'),
            'info_title'            =>     onapp_string('ADJUST_RESOURCE_ALLOCATIONS'),
            'info_body'             =>     onapp_string('ADJUST_RESOURCE_ALLOCATIONS_INFO'),

        );                                                                                             

        onapp_show_template( 'vm_edit', $params );
     }
       
    /**
     * Creates a new virtual machine
     *
     * @param object $onapp OnApp object
     * @return void
     */
     private function create()
     {
        global $_ALIASES;
        $onapp = $this->get_factory();

        onapp_permission(array('virtual_machines', 'virtual_machines.create'));
        $virtual_machine = onapp_get_arg('virtual_machine');                               
        
        $vm_obj = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        
        foreach($virtual_machine as $key => $value)
            if($virtual_machine[$key] != '' && ! is_null($virtual_machine[$key]))
                $vm_obj->$key = $value;                                           
                                                                                          
        $vm_obj->save();                                                                            //   print('<pre>'); print_r($vm_obj); print('</pre>');die();
                                                                                   
        if( is_null($vm_obj->error))
        {   
            $_SESSION['message'] = 'VIRTUAL_MACHINE_HAS_BEEN_CREATED_SUCCESSFULLY';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=details&id=' . $vm_obj->_id );
        }
        else
            $this->show_template_view( $vm_obj->error);
     }

    /**
     * Startups virtual machine
     * 
     * @param integer virtual machine Id
     * @return void
     */
     private function startup($id)
     {
        onapp_permission(array('virtual_machines', 'virtual_machines.power.own', 'virtual_machines.power'));
        global $_ALIASES;
        $onapp = $this->get_factory();
        
        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);

        $mode = onapp_get_arg('mode');

        switch ($mode)
        {
            case 'recovery':
                $virtual_machine->_id = $id;
                $virtual_machine->startup( true );
                break;
            default:
                $virtual_machine->_id = $id;
                $virtual_machine->startup();
                break;
        }
        
        if( is_null($virtual_machine->error))
        {
            $_SESSION['message'] = 'VIRTUAL_MACHINE_STARTUP_HAS_BEEN_QUEUED';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=details&id=' . $id );
        }
        else
            $this->show_template_view( $virtual_machine->error );
     }

    /**
     * Resets Virtual Machine Root Password
     *
     * @param integer virtual machine id
     * @return void
     */
     private function reset_password($id)
     {
        global $_ALIASES;
        $onapp = $this->get_factory();
        
        onapp_permission(array('virtual_machines.reset_root_password', 'virtual_machines.reset_root_password.own'));

            $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
            $virtual_machine->_id = $id;
            $virtual_machine->reset_password();

            if( is_null($virtual_machine->error))
            {
                $_SESSION['message'] = 'VIRTUAL_MACHINE_PASSWORD_WIL_BE_CHANGED_SHORTLY';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=details&id=' . $id );
            }
            else
                $this->show_template_view( $virtual_machine->error);
     }

    /**
     * Reboots Virtual Machine
     *
     * @param integer virtual machine id
     * @return void
     */
     private function reboot($id)
     {
        global $_ALIASES;
        $onapp = $this->get_factory();

        onapp_permission(array('virtual_machines', 'virtual_machines.power.own', 'virtual_machines.power'));
        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        
        $mode = onapp_get_arg('mode');
        
        switch ($mode)
        {
            case 'recovery':
                $virtual_machine->_id = $id;
                $virtual_machine->reboot( true );
                break;
            default:
                $virtual_machine->_id = $id;
                $virtual_machine->reboot();
                break;
        }
                                                                 
        if( is_null($virtual_machine->error))
        {   
            $_SESSION['message'] = 'VIRTUAL_MACHINE_WILL_BE_REBOOTED_SHORTLY';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=details&id=' . $id );
        }
        else
            $this->show_template_view( $virtual_machine->error);
     }

     /**
     * Deletes Virtual Machine
     *
     * @param integer virtual machine id
     * @return void
     */
     private function delete($id)
     {
        global $_ALIASES;
        $onapp = $this->get_factory();

        onapp_permission(array('virtual_machines', 'virtual_machines.delete', 'virtual_machines.delete.own'));
        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $virtual_machine->_id = $id;
        $virtual_machine->delete();

        if( is_null($virtual_machine->error))
        {
            $_SESSION['message'] = 'VIRTUAL_MACHINE_HAVE_BEEN_SQUEDULED_FOR_DESTRUCTION';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=details&id=' . $id );
        }
        else
            $this->show_template_view( $virtual_machine->error);
     }
     
    /**
     * Disables virtual machine disk autobackups
     *
     * @param integer virtual machine disk id
     * @return void
     */
     private function autobackup_disable( $id )
     {
        onapp_permission(array('disks.autobackup', 'disks.autobackup.own'));

        global $_ALIASES;
        $onapp = $this->get_factory();
  
        $disk = $onapp->factory('Disk', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $disk->disableAutobackup( $id );                                                            

        if( is_null($disk->error))
        {
            $_SESSION['message'] = 'AUTOBACKUP_HAS_BEEN_DISABLED_FOR_THIS_DISK';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=disks&id=' . onapp_get_arg('virtual_machine_id') );
        }
        else
            $this->show_template_disks( onapp_get_arg('virtual_machine_id'), $disk->error);
     }

    /**
     * Enables virtual machine disk autobackups
     *
     * @param integer virtual machine disk id
     * @return void
     */
     private function autobackup_enable( $id )
     {
        onapp_permission(array('disks.autobackup', 'disks.autobackup.own'));

        global $_ALIASES;
        $onapp = $this->get_factory();

        $disk = $onapp->factory('Disk', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $disk->enableAutobackup( $id );

        if( is_null($disk->error))
        {
            $_SESSION['message'] = 'AUTOBACKUP_HAS_BEEN_ENABLED_FOR_THIS_DISK';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=disks&id=' . onapp_get_arg('virtual_machine_id') );
        }
        else
            $this->show_template_disks( onapp_get_arg('virtual_machine_id'), $disk->error);
     }
   
    /**
     * Shutdowns Virtual Machine
     *
     * @param integer virtual machine id
     * @return void
     */
     private function shutdown($id)
     {
        global $_ALIASES;
        $onapp = $this->get_factory();

        onapp_permission(array('virtual_machines', 'virtual_machines.power', 'virtual_machines.power.own'));
        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $virtual_machine->_id = $id;   
        $virtual_machine->shutdown( );


        if( is_null($virtual_machine->error))
        { 
            $_SESSION['message'] = 'VIRTUAL_MACHINE_WILL_BE_STOPED_SHORTLY';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=details&id=' . $id );
        }
        else
            $this->show_template_view( $virtual_machine->error);
     }

     /**
     * Suspends virtual machine
     *
     * @param integer virtual machine id
     * @return void
     */
     private function suspend($id)
     {
        global $_ALIASES;
        $onapp = $this->get_factory();

        onapp_permission(array('virtual_machines', 'virtual_machines.suspend'));
        
        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $virtual_machine->_id = $id; 
        $virtual_machine->suspend();

        onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=details&id=' . $id );
     }
     
     /**
     * Builds virtual machine
     *
     * @param integer virtual machine id
     * @return void
     */
     private function build($id)
     {
        global $_ALIASES;
        $onapp = $this->get_factory();

        onapp_permission(array('virtual_machines', 'virtual_machines.create'));
 
        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $virtual_machine->_id = $id; 
        $virtual_machine->build();
        
        if( is_null($virtual_machine->error))
        {
            $_SESSION['message'] = 'VIRTUAL_MACHINE_BUILD_HAS_BEEN_QUEUED';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=details&id=' . $id );
        }
        else
            $this->show_template_view( $virtual_machine->error);
     }

    /**
     * Deletes virtual machine backup
     *
     * @param integer backup id
     * @return void
     */
     private function backup_delete($id)
     {
        onapp_permission(array('backups.delete', 'backups'));
        
        global $_ALIASES;
        $onapp = $this->get_factory();

        $backup = $onapp->factory('VirtualMachine_Backup', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $backup->_id = $id;
        $backup_obj = $backup->delete($id);

        if( is_null($backup->error))
        {
            $_SESSION['message'] = 'BACKUP_HAS_BEEN_SQUEDULED_FOR_REMOVAL';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']);
        }
        else
            $this->show_template_view( $backup->error);
     }
     
    /**
     * Takes disk backup
     *
     * @param integer disk id
     * @return void
     */
     private function backup_take( $id )
     {
        onapp_permission(array('backups.create', 'backups'));
        
        global $_ALIASES;
        $onapp = $this->get_factory();

        $disk = $onapp->factory('Disk', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $disk->takeBackup( $id );                                          // print('<pre>');print_r($disk); print('</pre>'); die();

        if( is_null($disk->error))
        {
            $_SESSION['message'] = 'BACKUP_HAS_BEEN_CREATED_AND_WILL_BE_TAKEN_SHORTLY';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']. '?action=disk_backups&id=' . onapp_get_arg('virtual_machine_id') );
        }
        else
            $this->show_template_disk_backup( onapp_get_arg('virtual_machine_id'), $disk->error );
     }
     
    /**
     * Deletes virtual machine firewall rule
     *
     * @param integer firewall rule id
     * @return void
     */
     private function firewall_rule_delete( $id )
     {
        onapp_permission(array('firewall_rules.delete', 'firewall_rules', 'firewall_rules.delete.own'));
        
        global $_ALIASES;
        $onapp = $this->get_factory();
        
        $virtual_machine_id = onapp_get_arg('virtual_machine_id');

        $firewall = $onapp->factory('VirtualMachine_FirewallRule', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $firewall->_id = $id;
        $firewall->_virtual_machine_id = $virtual_machine_id;
        $firewall_obj = $firewall->delete($id);                       //print('<pre>');print_r($firewall); print('</pre>'); die();

        if( is_null($firewall->error) )
        {
            $_SESSION['message'] = 'RULE_HAS_BEEN_DESTROYED';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']. '?action=firewall&id=' . $virtual_machine_id);
        }
        else
            $this->show_template_firewall( $virtual_machine_id, $firewall->error );
     }
     
    /**
     * Restores virtual machine backup
     *
     * @param integer backup id
     * @return void
     */
     private function backup_restore($id)
     {
        global $_ALIASES;
        $onapp = $this->get_factory();

        onapp_permission(array('backups.convert', 'backups.convert.own', 'backups'));

        $backup = $onapp->factory('VirtualMachine_Backup', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $backup->_id = $id;
        $backup_obj = $backup->restore( );

        if( is_null($backup->error))
        {
            $_SESSION['message'] = 'BACKUP_HAS_BEEN_SQUEDULED_FOR_RESTORE';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']);
        }
        else
            $this->show_template_view( $backup->error);
     }

    /**
     * Converts virtual machine backup to template
     *
     * @param integer backup id
     * @return void
     */
     private function backup_convert($id )
     { 
        onapp_permission(array('backups.convert', 'backups.convert.own', 'backups'));
        
        $label = onapp_get_arg('label');
        
        if(is_null($label))
            $this->show_template_backup_convert( $id );
        else
        {
            global $_ALIASES;
            $onapp = $this->get_factory();
            
            $backup = $onapp->factory('VirtualMachine_Backup', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
            $backup->_id = $id; 
            $backup_obj = $backup->convert( $label ); 
            
            if( is_null($backup->error))
            {
                $_SESSION['message'] = 'BACKUP_HAS_BEEN_SQUEDULED_FOR_CONVERTION';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']);
            }
            else
                $this->show_template_view( $backup->error);
        }
     }

    /**
     * Edits Administrator's note
     *
     * @param integer virtual machine id
     * @return void
     */
     private function edit_admin_note( $id )
     {   //echo $id; die();
        onapp_permission('virtual_machines', 'sysadmin_tools');

        $note = onapp_get_arg('note');

        if( is_null($note) )
            $this->show_template_edit_admin_note( $id );
        else
        {                                                                    //  print('<pre>');print_r($note); print('</pre>'); die();
            global $_ALIASES;
            $onapp = $this->get_factory();

            $vm = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
            $vm->editAdminNote( $id, $note );                                          // print('<pre>');print_r($vm); print('</pre>'); die();

            if( is_null($vm->error))
            {
                $_SESSION['message'] = 'RESOURCES_UPDATED_SUCCESSFULLY';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] .'?action=details&id=' . $id);
            }
            else
                $this->show_template_view( $id, $vm->error);
        }
     }
     
    /**
     * Edits virtual machine disk
     *
     * @param integer virtual machine disk id
     * @return void
     */
     private function disk_edit( $id )
     { 
        onapp_permission(array('disks', 'disks.update', 'disks.update.own'));
        
        $disk = onapp_get_arg('disk');
        
        if(is_null($disk))
            $this->show_template_disk_edit( $id );
        else
        {                                                                                 //print_r($disk); die();
            global $_ALIASES;
            $onapp = $this->get_factory();
            
            $disk_obj = $onapp->factory('Disk', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
            foreach( $disk as $key => $value )
                $disk_obj->$key = $value;

            $disk_obj->save();
                                                                                              //print('<pre>');print_r($disk_obj); print('</pre>'); die();
            if( is_null($disk_obj->error))
            {
                $_SESSION['message'] = 'DISK_RESIZE_HAS_BEEN_REQUESTED';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=disks&id=' . onapp_get_arg('virtual_machine_id') );
            }
            else
                $this->show_template_view( $disk_obj->error);
        }
     }
     
    /**
     * Edits disk backups schedule
     *
     * @param integer virtual machine disk id
     * @return void
     */
     private function disk_backups_schedule_edit( $id )
     { 
        onapp_permission(array('schedules', 'schedules.update'));
        
        $schedule = onapp_get_arg('schedule');
        
        if(is_null($schedule))
            $this->show_template_disk_backups_schedule_edit( $id );
        else
        {                                                                                             // print_r($schedule); die();
            global $_ALIASES;
            $onapp = $this->get_factory();
           
            $schedule_obj = $onapp->factory('Disk_Schedule', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
            foreach( $schedule as $key => $value )
                $schedule_obj->$key = $value;                                                          // print('<pre>');print_r($schedule_obj); print('</pre>'); die();

            $schedule_obj->save();
                                                                                                       //print('<pre>');print_r($schedule_obj); print('</pre>'); die();
            if( is_null($schedule_obj->error))
            {
                $_SESSION['message'] = 'SCHEDULE_WAS_SUCCESSFULLY_UPDATED';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=disk_backups_schedule&id=' . $id );
            }
            else
                $this->show_template_disk_backups_schedule( $id, $schedule_obj->error);
        }
     }
     
    /**
     * Edits virtual machine firewall rule
     *
     * @param integer virtual machine firewall rule id
     * @return void
     */
     private function firewall_rule_edit( $id )
     { 
        onapp_permission(array('firewall_rules', 'firewall_rules.update', 'firewall_rules.update.own'));
        
        $virtual_machine_id = onapp_get_arg('virtual_machine_id');
        
        $firewall = onapp_get_arg('firewall');
        
        if(is_null($firewall))
            $this->show_template_firewall_rule_edit( $id );
        else
        {                                                                                             // print_r($firewall); die();
            global $_ALIASES;
            $onapp = $this->get_factory();
           
            $firewall_obj = $onapp->factory('VirtualMachine_FirewallRule', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
            foreach( $firewall as $key => $value )
                $firewall_obj->$key = $value;                                                           //print('<pre>');print_r($firewall_obj); print('</pre>'); die();

            $firewall_obj->save();
                                                                                                       //print('<pre>');print_r($schedule_obj); print('</pre>'); die();
            if( is_null($firewall->error))
            {
                $_SESSION['message'] = 'RULE_HAS_BEEN_UPDATED';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=firewall&id=' . $virtual_machine_id );
            }
            else
                $this->show_template_disk_backups_schedule( $virtual_machine_id, $firewall->error);
        }
     }
      
    /**
     * Creates new disk backups schedule
     *
     * @param integer virtual machine disk id
     * @return void
     */
     private function disk_backups_schedule_create( $id )
     { 
        onapp_permission(array('schedules', 'schedules.create'));
        
        $schedule = onapp_get_arg('schedule');
        
        if(is_null($schedule))
            $this->show_template_disk_backups_schedule_create( $id );
        else
        {                                                                               //  print_r($schedule); die();
            global $_ALIASES;
            $onapp = $this->get_factory();
            
            $schedule_obj = $onapp->factory('Disk_Schedule', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
            foreach( $schedule as $key => $value )
                $schedule_obj->$key = $value;                                                          // print('<pre>');print_r($schedule_obj); print('</pre>'); die();

            $schedule_obj->save();
                                                                                             // print('<pre>');print_r($schedule_obj); print('</pre>'); die();
            if( is_null($schedule_obj->error))
            {
                $_SESSION['message'] = 'SCHEDULE_HAS_BEEN_CREATED';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=disk_backups_schedule&id=' . $id );
            }
            else
                $this->show_template_disk_backups_schedule( $id, $schedule_obj->error);
        }
     }
     
    /**
     * Creates firewall rule for virtual machine
     *
     * @param integer virtual machine id
     * @return void
     */
     private function firewall_rule_create( $id )
     { 
        onapp_permission(array('firewall_rules', 'firewall_rules.create'));
        
        $firewall = onapp_get_arg('firewall');
                                                                              // print_r($firewall); die();
        global $_ALIASES;
        $onapp = $this->get_factory();
            
        $firewall_obj = $onapp->factory('VirtualMachine_FirewallRule', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        foreach( $firewall as $key => $value )
            $firewall_obj->$key = $value;                                                          // print('<pre>');print_r($firewall_obj); print('</pre>'); die();

        $firewall_obj->save();
                                                                                             //print('<pre>');print_r($firewall_obj); print('</pre>'); die();
        if( is_null($firewall_obj->error))
        {
            $_SESSION['message'] = 'RULE_HAS_BEEN_ADDED';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=firewall&id=' . $id );
        }
        else
            $this->show_template_firewall( $id, $firewall_obj->error);
    }
    
    /**
     * Updates default firewall rule for network interface
     *
     * @param integer virtual machine id
     * @return void
     */
     private function firewall_rule_update_defaults( $id )
     { 
        onapp_permission(array('networks', 'networks.update'));
        
        $firewall = onapp_get_arg('firewall');
                                                                            //  print_r($firewall); die();
        global $_ALIASES;
        $onapp = $this->get_factory();
            
        

        

        $firewall_obj = $onapp->factory('VirtualMachine_FirewallRule', ONAPP_WRAPPER_LOG_REPORT_ENABLE);           // print('<pre>');print_r($network_interfaces); print('</pre>'); die();

        $firewall_obj->updateDefaults($id, $firewall);                                               //  print('<pre>');print_r($firewall); print('</pre>'); die();
                                                                                                                  
        if( is_null($firewall->error))
        {
            $_SESSION['message'] = 'DEFAULT_RULES_HAVE_BEEN_UPDATED';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=firewall&id=' . $id );
        }
        else
            $this->show_template_firewall( $id, $firewall->error);
    }
      
    /**
     * Creates a new disk on virtual machine
     *
     * @param integer virtual machine id
     * @return void
     */
     private function disk_create( $id )
     {
        onapp_permission(array('disks', 'disks.create'));

        $disk = onapp_get_arg('disk');

        if(is_null($disk))
            $this->show_template_disk_create( $id );
        else
        {          //print('<pre>');print_r($disk); print('</pre>'); die();
            global $_ALIASES;
            $onapp = $this->get_factory();

            $disk_obj = $onapp->factory('Disk', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
            foreach( $disk as $key => $value )
                $disk_obj->$key = $value;

            $disk_obj->save();

            $disk_obj->_disk_size = 2;
            $disk_obj->save();
                                                                                         //     print('<pre>');print_r($disk_obj); print('</pre>'); die();
            if( is_null($disk_obj->error))
            {
                $_SESSION['message'] = 'DISK_HAS_BEEN_ADDED_SUCCESSFULLY';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']  . '?action=disks&id=' . $id);
            }
            else
                $this->show_template_view( $disk_obj->error);
        }
     }

    /**
     * Changes virtual machine owner
     *
     * @param integer virtual machine id
     * @return void
     */
     private function change_owner( $id )
     {
        onapp_permission(array('virtual_machines', 'virtual_machines.change_owner'));

        global $_ALIASES;
        $onapp = $this->get_factory();

        $user_id = onapp_get_arg('user_id');

        if(is_null($user_id))
            $this->show_template_change_owner( $id );
        else
        { 
            $vm = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
            $vm->_id = $id;
            $vm->_user_id = $user_id;
            $vm->change_owner( $user_id );                                                 // print('<pre>');print_r($vm); print('</pre>'); die();

            if( is_null($vm->_obj["errors"]))
            {
                $_SESSION['message'] = 'VIRTUAL_MACHINE_OWNER_HAS_BEEN_CHANGED_SUCCESSFULLY';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']. '?action=details&id=' .$id);
            }
            else
                $this->show_template_details($id, $vm->_obj["errors"]);
        }
     }
     
    /**
     * Moves to the upper or lower position the virtual machine firewall rule 
     *
     * @param integer virtual machine firewall rule id
     * @return void
     */
     private function firewall_rule_move( $id )
     {
        onapp_permission(array('firewall_rules.update', 'firewall_rules.update.own', 'firewall_rules'));

        global $_ALIASES;
        $onapp = $this->get_factory();

        $virtual_machine_id = onapp_get_arg('virtual_machine_id');
        $position = onapp_get_arg('position');

        $firewall = $onapp->factory('VirtualMachine_FirewallRule', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $firewall->_virtual_machine_id = $virtual_machine_id;
        $firewall->_id = $id;
        $firewall->move( $position );                                                // print('<pre>');print_r($firewall); print('</pre>'); die();

        if( is_null($firewall->errors))
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']. '?action=firewall&id=' .$virtual_machine_id);
        else
             $this->show_template_firewall($virtual_machine_id, $firewall->errors);
     }
     
    /**
     * Edits network interface for virtual machine
     *
     * @param integer network_interface id
     * @return void
     */
     private function network_interface_edit( $id )
     {
        onapp_permission( array( 'networks', 'networks.update' ) );
        
        $network_interface = onapp_get_arg('network_interface');   //print_r($network_interface); die();
        
        if( is_null( $network_interface ) )
            $this->show_template_network_interface_edit( $id );
        else
        {
            global $_ALIASES;
            $onapp = $this->get_factory();
                                                                                                                           
            $network_interface_obj = $onapp->factory('VirtualMachine_NetworkInterface', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
            
            foreach($network_interface as $key => $value)
                $network_interface_obj->$key = $value;
            
            $network_interface_obj->save( );                                                     //print('<pre>');print_r($network_interface_obj); print('</pre>'); die();
            
            if( is_null($network_interface_obj->error))
            {
                $_SESSION['message'] = 'NETWORK_INTERFACE_HAVE_BEEN_UPDATED_SUCCESSFULLY';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']  . '?action=network_interfaces&id=' . $network_interface['_virtual_machine_id']);
            }
            else
                $this->show_template_network_interface($network_interface['_virtual_machine_id'], $network_interface_obj->error);
        }
     }

    /**
     * Adjust virtual machine resource allocation
     *
     * @param integer virtual machine id
     * @return void
     */
     private function edit( $id )
     {
        onapp_permission( array( 'virtual_machines', 'virtual_machines.update', 'virtual_machines.update.own' ) );

        $virtual_machine = onapp_get_arg('virtual_machine');                                                         //print_r($virtual_machine); die();

        if( is_null( $virtual_machine ) )
            $this->show_template_edit( $id );
        else
        { 
            global $_ALIASES;
            $onapp = $this->get_factory();

            $vm = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);

            foreach($virtual_machine as $key => $value)
                $vm->$key = $value;

            $vm->save( );                                                    // print('<pre>');print_r($vm); print('</pre>'); die();

            if( is_null($vm->error))
            {
                $_SESSION['message'] = 'RESOURCES_UPDATED_SUCCESSFULLY';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']  . '?action=details&id=' . $id);
            }
            else
                $this->show_template_details($id, $vm->error);
        }
     }
     
    /**
     * Creates network interface for virtual machine
     *
     * @param integer virtual machine id
     * @return void
     */
     private function network_interface_create( $id )
     {
        onapp_permission( array( 'networks', 'networks.create' ) );
        
        $network_interface = onapp_get_arg('network_interface');
        
        if( is_null( $network_interface ) )
            $this->show_template_network_interface_create( $id );
        else
        {                                                                           //print_r($network_interface); die();
            global $_ALIASES;
            
            $onapp = $this->get_factory();
        
                                                                                
            $network_interface_obj = $onapp->factory('VirtualMachine_NetworkInterface', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
            
            foreach($network_interface as $key => $value)
                $network_interface_obj->$key = $value;
            
            $network_interface_obj->save( );                                                       //print('<pre>');print_r($network_interface_obj); print('</pre>'); die();
            
            if( is_null($network_interface_obj->error))
            {
                $_SESSION['message'] = 'NETWORK_INTERFACE_HAS_BEEN_CREATED_SUCCESSFULLY';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']  . '?action=network_interfaces&id=' . $id);
            }
            else
                $this->show_template_network_interface($id, $network_interface_obj->error);
                
        }
     }

    /**
     * Deletes virtual machine ip_address assignment
     *
     * @param integer ip_address id
     * @return void
     */
     private function ip_address_delete($id)
     {
        onapp_permission(array('ip_address_joins.delete', 'ip_address_joins'));

        global $_ALIASES;
        $onapp = $this->get_factory();
        
        $virtual_machine_id = onapp_get_arg('virtual_machine_id');

        $ip_address = $onapp->factory('VirtualMachine_IpAddressJoin', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $ip_address->_virtual_machine_id = $virtual_machine_id;
        $ip_address->_id = $id;
        $ip_address_obj = $ip_address->delete( $id );                                                        //print('<pre>'); print_r($ip_address);die(); print('</pre>');

        if( is_null($ip_address->error))
        {
            $_SESSION['message'] = 'IP_ADDRESS_ASSEGNMENT_HAS_BEEN_REMOVED_SUCCESSFULLY';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines'] . '?action=ip_addresses&id=' . $virtual_machine_id );
        }
        else
            $this->show_template_ip_addresses($virtual_machine_id, $ip_address->error);
     }
     
    /**
     * Deletes network interface for virtual machine 
     *
     * @param integer network interface id
     * @return void
     */
     private function network_interface_delete( $id )
     {
        onapp_permission(array('networks.delete', 'networks'));
        
        $virtual_machine_id = onapp_get_arg('virtual_machine_id');
        
        global $_ALIASES;
        $onapp = $this->get_factory();

        $network_interface = $onapp->factory('VirtualMachine_NetworkInterface', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $network_interface->_id = $id;
        $network_interface->_virtual_machine_id = $virtual_machine_id;
        $network_interface_obj = $network_interface->delete( $id );                                                        //print('<pre>'); print_r($ip_address);die(); print('</pre>');

        if( is_null($network_interface->error))
            {
                $_SESSION['message'] = 'NETWORK_INTERFACE_HAVE_BEEN_REMOVED_SUCCESSFULLY';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']  . '?action=network_interfaces&id=' . $virtual_machine_id);
            }
            else
                $this->show_template_network_interface($virtual_machine_id, $network_interface_obj->error);
     }

    /**
     * Deletes virtual machine disks
     *
     * @param integer virtual machine disk id
     * @return void
     */
     private function disk_delete( $id )
     {
        onapp_permission(array('disks', 'disks.delete.own', 'disks.delete'));

        $virtual_machine_id = onapp_get_arg('virtual_machine_id');

        global $_ALIASES;
        $onapp = $this->get_factory();

        $disk = $onapp->factory('Disk', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $disk->_id = $id;
        $disk->_virtual_machine_id = $virtual_machine_id;
        $disk->delete( $id );                                                                 //print('<pre>'); print_r($disk);die(); print('</pre>');

        if( is_null($disk->error))
            {
                $_SESSION['message'] = 'DISK_HAS_BEEN_SCHEDULED_FOR_DESTRUCTION';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']  . '?action=disks&id=' . $virtual_machine_id);
            }
            else
                $this->show_template_network_interface($virtual_machine_id, $network_interface_obj->error);
     }
     
    /**
     * Deletes disk backup schedule
     *
     * @param integer disk backup schedule id
     * @return void
     */
     private function disk_backups_schedule_delete( $id )
     {
        onapp_permission(array('schedules', 'schedules.delete'));

        $disk_id = onapp_get_arg('disk_id');

        global $_ALIASES;
        $onapp = $this->get_factory();

        $schedule = $onapp->factory('Disk_Schedule', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $schedule->_id = $id;
        $schedule->delete( $id );                                                                 //print('<pre>'); print_r($disk);die(); print('</pre>');

        if( is_null($schedule->error))
            {
                $_SESSION['message'] = 'SCHEDULE_HAS_BEEN_DESTRUCTED';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']  . '?action=disk_backups_schedule&id=' . $disk_id);
            }
            else
                $this->show_template_disk_backups_schedule($disk_id, $schedule->error);

     }
     
    /**
     * Applies / Updates virtual machine firewall rules
     *
     * @param integer virtual machine id
     * @return void
     */
     private function firewall_rules_apply( $id )
     {
        onapp_permission(array('firewall_rules', 'firewall_rules.update'));

        global $_ALIASES;
        $onapp = $this->get_factory();

        $firewall = $onapp->factory('VirtualMachine_FirewallRule', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $firewall->update( $id );                                                                // print('<pre>'); print_r($firewall); print('</pre>');die();

        if( is_null($firewall->error))
            {
                $_SESSION['message'] = 'AN_UPDATE_OF_FIREWALL_RULES_HAS_BEEN_QUEUED';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']  . '?action=details&id=' . $id);
            }
            else
                $this->show_template_details($id, $schedule->error);
     }
      
    /**
     * Rebuilds network for virtual machine
     *
     * @param integer virtual machine id
     * @return void
     */
     private function rebuild_network($id)
     {  
        onapp_permission(
            array(
                'virtual_machines.rebuild_network',
                'virtual_machines.rebuild_network.own',
                'virtual_machines'
            )
        );

        global $_ALIASES;
        $onapp = $this->get_factory();

        $virtual_machine = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $virtual_machine->_id = $id;
        $virtual_machine->rebuild_network( $id );                                     

        if( is_null($virtual_machine->error))
        {
            $_SESSION['message'] = 'NETWORK_INTERFACE_WILL_BE_REBUILD_FOR_THIS_VIRTUAL_MACHINE';
            onapp_redirect(
                ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']
                . '?action=ip_addresses&id=' . $id
            );
        }
        else
            $this->show_template_ip_addresses($id, $ip_address->error);
     }

    /**
     * Assigns new IP Address to virtual machine
     *
     * @param interger virtual machine id
     * @return void
     */
     private function ip_address_join_new($id)
     {   
        onapp_permission(array('ip_address_joins.create', 'ip_address_joins', 'ip_address_joins.create.own'));
        
        $ip_address = onapp_get_arg('ip_address');
        if( ! $ip_address ){
            $this->show_template_ip_address_join_new($id);
        }else{                                                         // print('<pre>'); print_r($ip_address); print('</pre>');die();
            global $_ALIASES;
            $onapp = $this->get_factory();
    
            $ip_address_obj = $onapp->factory('VirtualMachine_IpAddress', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
            foreach( $ip_address as $key => $value)
                    $ip_address_obj->$key = $value;
    
            $ip_address_obj->join( );                                   //   print('<pre>'); print_r($ip_address_obj); print('</pre>');die();

            if( is_null($ip_address_obj->error))
            {
                $_SESSION['message'] = 'IP_ADDRESS_HAS_BEEN_ASSEGNED_SUCCESSFULLY';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']. '?action=ip_addresses&id=' . $id);
            }
            else
                $this->show_template_ip_addresses( $id, $ip_address_obj->error );
        } 
     }

     /**
     * Assigns new IP Address to virtual machine
     *
     * @param interger virtual machine id
     * @return void
     */
     private function migrate($id)
     {
        onapp_permission(array('virtual_machines', 'virtual_machines.migrate', 'virtual_machines.migrate.own'));

        $virtual_machine = onapp_get_arg('virtual_machine');
        if( ! $virtual_machine ){
            $this->show_template_migrate($id);
        }else{                                                          //print('<pre>'); print_r($virtual_machine); print('</pre>');die();
            global $_ALIASES;
            $onapp = $this->get_factory();

            $vm = $onapp->factory('VirtualMachine', ONAPP_WRAPPER_LOG_REPORT_ENABLE); 
            $vm->migrate( $id, $virtual_machine['_destination_id']);
                                                                                       // print('<pre>'); print_r($vm); print('</pre>');die();
            if( is_null( $vm->error ) )
            {
                $_SESSION['message'] = 'VIRTUAL_MACHINE_MIGRATE_HAS_BEEN_QUEUED';
                onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['virtual_machines']. '?action=details&id=' . $id);
            }
            else
                $this->show_template_details( $id, $vm->error );
        }
     }

    /**
     * Calculates backups' quantity and sizes
     *
     * @param object $vm_backup_obj Virtual Machine Backup object
     * @return array Virtual Machine backups' quantity and sizes
     */
     private function calculateBackups($vm_backup_obj)
     {
        $backups_quantity = 0;
        $backups_total_size = 0;
        if($vm_backup_obj && is_array($vm_backup_obj))
        {
            foreach( $vm_backup_obj as $value )
            {
                $backups_quantity++;
                $backups_total_size += $value->_backup_size;
            }
            $backups_total_size = round( $backups_total_size / 1024 );
        }
        else if(count($vm_backup_obj) == 1 && ! is_array( $vm_backup_obj ) && $vm_backup_obj->_id)
        {    
            $backups_quantity = 1; 
            $backups_total_size = round( $vm_backup_obj->_backup_size / 1024 );
        }
        
        return $size_and_quantity = array(
            'size'        => $backups_total_size,
            'quantity'    => $backups_quantity
        );
     }

   /**
     * Checks necessary access to this class
     *
     * @return boolean [true|false]
     */
     static function  access(){
        return onapp_has_permission(array('virtual_machines', 'virtual_machines.read.own'));
    }
}