import math

import pandas as pd
from database_handler import DatabaseHandler
import globalContants as gc
import math

__table_nutrition_filename__ = r'C:\Users\ShayMoshe\PycharmProjects\myFoodsNutrition\csvFiles\table_nutrition_attribute.xlsx'


def create_table_nutrition_attribute(dbh : DatabaseHandler):
    # Nutrition attributes with nut. unique ID, nut. name, nut. daily Goal, nut. daily UL, nut display units, additional names (english/hebrew)?
    tableName = gc.__table_nutrition_attribute_name__
    # ColNamesNTypes = {'nutritionUID'    : 'int',
    #                   'nutritionName'   : 'str',
    #                   'nutritionDGoal'  :'float',
    #                   'nutritionDUL'    : 'float',
    #                   'nutritionDisplayUnits'   : 'str',
    #                   'additionalNames' : 'str',
    #                   'hebrewDisplayName'       : 'str',
    #                   'isDisplayed'     : 'int'
    #                   }

    df = pd.read_excel(__table_nutrition_filename__,
                       sheet_name='table_nutrition_attribute')

    ColNamesNTypes = {df.keys()[ii]: str(type(df[df.keys()[ii]][0])).replace("<class '","").replace("'>","") for ii in range(len(df.keys()))}

    print(df)

    dbh.CreateTable(tableName,
                                  ColNamesNTypes,
                                  PrimaryKeyName='nutritionUID',
                                  isPrimaryAutoIncrement=True,
                                  colProp={'nutritionName' : 'UniqueIndex'},
                                  colDefValues = {'nutritionDGoal' : -1000, 'nutritionDUL' : -1000, 'additionalNames' : '', 'isDisplayed' : 1},
                                  ifExists='replace')

    key_names = list(ColNamesNTypes.keys())
    for row in df.iterrows():
        values_list = list(row[1][key_names])
        # print(row[1]['nutritionUID'])
        for ii in range(len(values_list)):
            if type(values_list[ii]) is not type('str'):
                if math.isnan(values_list[ii]):
                    values_list[ii] = ''
        dbh.addItem(tableName,key_names,values_list)

def update_table_items_data_for_nutritions(dbh : DatabaseHandler):
    food_items_columns_names = dbh.get_columns_names(gc.__table_items_data_name__)
    current_item_nutritions = []
    for item in food_items_columns_names:
        if item.startswith('_'):
            current_item_nutritions.append(item.strip('_'))
    # print(current_item_nutritions)
    required_nutritions = dbh.loadAllRows(gc.__table_nutrition_attribute_name__,['nutritionName'])
    required_nutritions = [required_nutrition[0] for required_nutrition in required_nutritions]
    # print(required_nutritions)
    additional_nutritions_to_fill = []
    for required_nutrition in required_nutritions:
        if required_nutrition not in current_item_nutritions:
            additional_nutritions_to_fill.append(required_nutrition)
    print(additional_nutritions_to_fill)
    colsNamesAndTypes = {f'_{name}': 'float' for name in additional_nutritions_to_fill}
    colDefValues = {f'_{name}': -1000 for name in additional_nutritions_to_fill}
    dbh.add_column(gc.__table_items_data_name__,
                   colsNamesAndTypes,
                   colDefValues)

dbh = DatabaseHandler(['pc'])
# create_table_nutrition_attribute(dbh)
# update_table_items_data_for_nutritions(dbh)
