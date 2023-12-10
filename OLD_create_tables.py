import csv
from database_handler import DatabaseHandler
import globalContants as gc

def create_table_nutrition_attribute(dbh : DatabaseHandler):
    # Nutrition attributes with nut. unique ID, nut. name, nut. daily Goal, nut. daily UL, nut display units, additional names (english/hebrew)?
    tableName = gc.__table_nutrition_attribute_name__
    ColNamesNTypes = {'nutritionUID'    : 'int',
                      'nutritionName'   : 'str',
                      'nutritionDGoal'  :'float',
                      'nutritionDUL'    : 'float',
                      'nutritionDisplayUnits'   : 'str',
                      'additionalNames' : 'str',
                      'hebrewDisplayName'       : 'str',
                      'isDisplayed'     : 'int'
                      }
    dbh.CreateTable(tableName,
                                  ColNamesNTypes,
                                  PrimaryKeyName='nutritionUID',
                                  isPrimaryAutoIncrement=True,
                                  colProp={'nutritionName' : 'UniqueIndex'},
                                  colDefValues = {'nutritionDGoal' : -1000, 'nutritionDUL' : -1000, 'additionalNames' : '', 'isDisplayed' : 1},
                                  ifExists='replace')

    ## Tmp - take it from previous DB
    eng_to_heb_dict = dict()
    with open('./csvFiles/eng_heb_terms_nut.csv', newline='') as csvfile:
        reader = csv.DictReader(csvfile)
        for row in reader:
            # print(row)
            eng_to_heb_dict[row['eng_name']] = row['heb_name']

    nutrition_display_units_dict = dict()
    with open('./csvFiles/conversion_nut_units_to_display.csv', newline='') as csvfile:
        reader = csv.DictReader(csvfile)
        for row in reader:
            # print(row)
            nutrition_name = row['keyCol']
            nutrition_display_unit = row['valueCol']
            if nutrition_name in eng_to_heb_dict.keys():
                nutrition_display_units_dict[nutrition_name] = nutrition_display_unit
            else:
                raise Exception("nutrition unit does not exists in eng_heb_list!")

    daily_nutrition_goals_dict = dict()
    additional_names_list = []
    with open('./csvFiles/daily_nutrition_goals.csv', newline='') as csvfile:
        reader = csv.DictReader(csvfile)
        for row in reader:
            # print(row)
            nutrition_name = row['keyCol']
            nutrition_goal = row['valueCol']
            if nutrition_name in eng_to_heb_dict.keys():
                daily_nutrition_goals_dict[nutrition_name] = nutrition_goal
            else:
                additional_names_list.append(nutrition_name)

    additional_names_dict={'fat': 'total_lipid_fat', 'alpha_linolenic_acid' : 'omega3', 'linoleic_acid' : 'omega6',
                           'vitamin_b9' : 'vitamin_b9_folic_acid',
                            'proteins' : 'protein',
                           'carbohydrates' : 'carbohydrate', 'dietary_fiber' : 'fiber_total_dietary'}

    for nutrition_name in eng_to_heb_dict.keys():
        if nutrition_name in additional_names_dict.values():
            additional_names_str_list = []
            additional_names_values_list = list(additional_names_dict.values())
            additional_names_keys_list = list(additional_names_dict.keys())
            for ii in range(len(additional_names_dict.values())):
                if (additional_names_values_list[ii] == nutrition_name):
                    additional_names_str_list.append(additional_names_keys_list[ii])
                    # print(additional_names_keys_list[ii])
                    additional_names_list.remove(additional_names_keys_list[ii])
            additional_names = ','.join(additional_names_str_list)
        else:
            additional_names = ''
        dbh.addItem(tableName,
                                  ['nutritionName','nutritionDGoal' ,'nutritionDUL','nutritionDisplayUnits',
                                            'additionalNames','hebrewDisplayName'],
                                  [nutrition_name,daily_nutrition_goals_dict[nutrition_name],-1000,nutrition_display_units_dict[nutrition_name],
                                   additional_names,eng_to_heb_dict[nutrition_name]]
                                  )

    assert(len(additional_names_list)==0)

    return True

def create_table_items_data(dbh : DatabaseHandler):
    # Items list with item's unique ID (auto index),  item name - index, in hebrew, (+additional names?), Nutrition Values, type/category (e.g. green veg., nuts, etc),
    #                           source(s), isExtended, isCombined, item description (including recipe?), item photo link?
    tableName = gc.__table_items_data_name__
    ColNamesNTypes = {'itemUID'    : 'int',
                      'itemName'   : 'str',
                      'additionalNames' : 'str',
                      'categoryType' : 'str',
                      'nutritionsVSource'  :'str',
                      'isExtended'     : 'int',
                      'itemsCombination'    : 'str',  # Optional - List of other items in case it is combined item
                      'itemDescription'   : 'str',
                      'itemPhotoLink'       : 'str',
                      }
    # Add nutrition items
    col_def_values_dic = {'itemsCombination' : '', 'itemDescription' : '', 'itemPhotoLink' : ''}
    with open('./csvFiles/table_nutrition_attribute.csv', newline='') as csvfile:
        reader = csv.DictReader(csvfile)
        for row in reader:
            ColNamesNTypes['_'+row['nutritionName']] = 'float'
            col_def_values_dic['_'+row['nutritionName']] = -1000


    dbh.CreateTable(tableName,
                                  ColNamesNTypes,
                                  PrimaryKeyName='itemUID',
                                  isPrimaryAutoIncrement=True,
                                  colProp={'itemName' : 'UniqueIndex'},
                                  colDefValues = col_def_values_dic,
                                  ifExists='replace')

    # Add nutrition sources link per item
    source_link_dict = dict()
    with open('./csvFiles/table_sources_links.csv', newline='') as csvfile:
        reader = csv.DictReader(csvfile)
        for row in reader:
            source_link_dict[row['item_name']] = row['url']

    with open('./csvFiles/db_items_nut.csv', newline='') as csvfile:
        reader = csv.DictReader(csvfile)
        for row in reader:
            ColNamesNValues = dict()
            for attr_name,attr_value in row.items():
                if (attr_name == 'itemName'):
                    ColNamesNValues[attr_name] = attr_value.strip()
                elif (attr_name=='_isExt'):
                    ColNamesNValues['isExtended'] = attr_value
                elif (attr_name == 'vitamin_b4'):
                    continue
                else:
                    ColNamesNValues['_'+attr_name] = attr_value
            ColNamesNValues['categoryType'] = 'general'
            if ColNamesNValues['itemName'] in source_link_dict:
                ColNamesNValues['nutritionsVSource'] = source_link_dict[ColNamesNValues['itemName']]
            else:
                print(ColNamesNValues['itemName']+' not exists!')
                ColNamesNValues['nutritionsVSource'] = ''
            ColNamesNValues['additionalNames'] = ''
            dbh.addItem(tableName,
                                      list(ColNamesNValues.keys()),
                                      list(ColNamesNValues.values())
                                      )
    return True

def create_table_daily_items():
    #   Daily items list - unique ID (auto Index), user unique ID?, list of date, time, mealTimeSlot, item, quantity
    tableName = gc.__table_daily_items_name__
    ColNamesNTypes = {'UID'        : 'int',
                      'userUID'    : 'int',
                      'itmDate'    :'date',
                      'itmTime'    : 'time',
                      'mealTimeSlot':'str',
                      'itemName'   : 'str',
                      'quantity'   : 'float',
                      }
    dbh.CreateTable(tableName,
                                  ColNamesNTypes,
                                  PrimaryKeyName='UID',
                                  isPrimaryAutoIncrement=True,
                                  colProp=None,
                                  ifExists='replace')
    return True
