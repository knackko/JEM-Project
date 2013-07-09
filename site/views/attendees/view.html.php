<?php
/**
 * @version 1.9 $Id$
 * @package JEM
 * @copyright (C) 2013-2013 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license GNU/GPL, see LICENSE.php

 * JEM is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.
 *
 * JEM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with JEM; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view');
/**
 * View class for the JEM attendees screen
 * @todo fix view
 * 
 * 
 * @package JEM
 * @since 0.9
 */
class JEMViewAttendees extends JViewLegacy {

	
	public function display($tpl = null)
	{
		$app =  JFactory::getApplication();

		if($this->getLayout() == 'print') {
			$this->_displayprint($tpl);
			return;
		}

		//initialise variables
		$db			=  JFactory::getDBO();
		$jemsettings = JEMHelper::config();
		$document	=  JFactory::getDocument();
		$user		=  JFactory::getUser();
		$params 	= $app->getParams();
		$menu		= $app->getMenu();
		$item		= $menu->getActive();
		$user			=  JFactory::getUser();
		$uri 		= JFactory::getURI();
		
		
		//redirect if not logged in
		if ( !$user->get('id') ) {
			$app->enqueueMessage(JText::_('COM_JEM_NEED_LOGGED_IN'), 'error');
			return false;
				
		}
		

		//add css file
		$document->addStyleSheet($this->baseurl.'/media/com_jem/css/jem.css');
		$document->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}, * html #jem dd { height: 1%; }</style><![endif]-->');
		
		//get vars
		$filter_order		= $app->getUserStateFromRequest( 'com_jem.attendees.filter_order', 'filter_order', 'u.username', 'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( 'com_jem.attendees.filter_order_Dir',	'filter_order_Dir',	'', 'word' );
		$filter_waiting	= $app->getUserStateFromRequest( 'com_jem.attendees.waiting',	'filter_waiting',	0, 'int' );
		$filter 			= $app->getUserStateFromRequest( 'com_jem.attendees.filter', 'filter', '', 'int' );
		$search 			= $app->getUserStateFromRequest( 'com_jem.attendees.search', 'search', '', 'string' );
		$search 			= $db->escape( trim(JString::strtolower( $search ) ) );

		//add css and submenu to document
		$document->addStyleSheet(JURI::root().'media/com_jem/css/backend.css');
		
		// Get data from the model
		$rows      	=  $this->get( 'Data');
		$pagination =  $this->get( 'Pagination' );
		$event 		=  $this->get( 'Event' );
		
		$params->def( 'page_title', $event->title);
		$pagetitle = $params->get('page_title');
		
		
		
		$pathway 	= $app->getPathWay();
		$pathway->setItemName(1, $item->title);
		$pathway->addItem('Att:'.$event->title);
		

		$print_link = JRoute::_('index.php?option=com_jem&amp;view=attendees&amp;layout=print&amp;task=print&amp;tmpl=component&amp;id='.$event->id);
		$backlink = 'attendees';
		$view = 'attendees';
		

		//build filter selectlist
		$filters = array();
		$filters[] = JHTML::_('select.option', '1', JText::_( 'COM_JEM_NAME' ) );
		$filters[] = JHTML::_('select.option', '2', JText::_( 'COM_JEM_USERNAME' ) );
		$lists['filter'] = JHTML::_('select.genericlist', $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $filter );

		// search filter
		$lists['search'] = $search;

		// waiting list status
		$options = array( JHTML::_('select.option', 0, JText::_('COM_JEM_ATT_FILTER_ALL')),
		                  JHTML::_('select.option', 1, JText::_('COM_JEM_ATT_FILTER_ATTENDING')),
		                  JHTML::_('select.option', 2, JText::_('COM_JEM_ATT_FILTER_WAITING')) ) ;
		$lists['waiting'] = JHTML::_('select.genericlist', $options, 'filter_waiting', 'onChange="this.form.submit();"', 'value', 'text', $filter_waiting);

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order']		= $filter_order;

		//assign to template
		$this->params			= $params;
		$this->lists 		= $lists;
		$this->rows 		= $rows;
		$this->pagination 	= $pagination;
		$this->event 		= $event;
		$this->pagetitle		= $pagetitle;
		$this->backlink		= $backlink;
		$this->view		= $view;
		$this->print_link		= $print_link;
		$this->item				= $item;
		$this->action				= $uri->toString();
		
		parent::display($tpl);
	}

	/**
	 * Prepares the print screen
	 *
	 * @param $tpl
	 *
	 * @since 0.9
	 */
	public function _displayprint($tpl = null)
	{
		$jemsettings = JEMHelper::config();
		$document	=  JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'media/com_jem/css/backend.css');
		
		//add css file
		$document->addStyleSheet($this->baseurl.'/media/com_jem/css/jem.css');
		$document->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}, * html #jem dd { height: 1%; }</style><![endif]-->');
		

		$rows      	=  $this->get( 'Data');
		$event 		=  $this->get( 'Event' );


		//assign data to template
		$this->rows 		= $rows;
		$this->event 		= $event;

		parent::display($tpl);
	}
	
	
	
}
?>