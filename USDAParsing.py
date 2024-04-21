import requests
import requests_cache
from bs4 import BeautifulSoup
#from myGui import showTable
import pandas as pd
from database_handler import DatabaseHandler
import globalContants as gc

## Nutirition Facts
# Omega3 and Omega6
# https://ods.od.nih.gov/factsheets/Omega3FattyAcids-HealthProfessional/#:~:text=The%20two%20major%20classes%20of,methyl%20group%20at%20the%20other.

__flagChachingSite__ = True
#__foodHebName__ = 'עגבניה'
#__dictHebrewToFNDDS__ = {"עגבניה": "tomato, raw",
#                    "בצל": "onion, raw"
#                     }

__myApiKey__ = 'cWjHlCbUgkl2VQ1IS32'+'DkuRuVZdLWsrmMztIvmQu'

def string_convert_to_search(str_inp : str,
                             flag_sort : bool = True,
                             sort_order_up_ndown : bool = False):
    str_lower = str_inp.lower()
    str_no_special = str_lower.replace(',',' ').replace('-',' ').replace('_',' ').replace('(',' ').replace(')',' ').replace(':','_')
    arr_words = str_no_special.split(' ')
    arr_words_no_empty = []
    for word in arr_words:
        if (len(word)>0):
            arr_words_no_empty.append(word)
    if (flag_sort):
        arr_words_no_empty.sort(reverse=sort_order_up_ndown)

    return '_'.join(arr_words_no_empty)

def convert_units_to_default_usda(value : float, units : str):
    ret_value = None
    match units:
        case 'G' | 'KCAL':
            ret_value = value
        case 'MG':
            ret_value = 1e-3*value
        case 'UG':
            ret_value = 1e-6*value
    if ret_value is None:
        raise Exception('unit '+units+' not found!')
    return ret_value

def get_nutrition_values(dbh : DatabaseHandler, myFoodName):
    # urlFoods = 'curl https://api.nal.usda.gov/fdc/v1/foods/search?api_key=DEMO_KEY&query=Cheddar%20Cheese'

    # myFoodName = "tomato, raw"  # "tomato"
    urlFoodsGet = "https://api.nal.usda.gov/fdc/v1/foods/search?api_key={ApiKey}&query={foodName}&dataType=Survey (FNDDS)".format(
        ApiKey=__myApiKey__, foodName=myFoodName)

    session = requests_cache.CachedSession('cacheUrlName')
    r = session.get(urlFoodsGet)
    if not r.from_cache:
        print(f'{urlFoodsGet} not from cache!')
    # r = requests.get(url=urlFoodsGet) # TODO: Change to cached
    # print(r)

    stored_nutrition_names = dbh.loadAllRows(gc.__table_nutrition_attribute_name__,['nutritionName','additionalNames'])
    main_stored_nutrition_names = [stored_nutrition_names[ii][0] for ii in range(len(stored_nutrition_names))]
    additional_nutrition_names = [stored_nutrition_names[ii][1] for ii in range(len(stored_nutrition_names))]
    additional_names_to_nurtition_dict = dict()
    for ii in range(len(additional_nutrition_names)):
        add_name = additional_nutrition_names[ii]
        if len(add_name)>1:
            add_name_arr = add_name.split(',')
            for add_name_single in add_name_arr:
                add_name_single = add_name_single.strip()
                additional_names_to_nurtition_dict[add_name_single] = main_stored_nutrition_names[ii]
    nutrition_attrs_USDA_list = []
    for food in r.json().get('foods'):
        tmp_data_list = food.get('foodNutrients')
        nutrition_attrs_USDA = dict()
        for nutrition_attr in tmp_data_list:
            # Covert name
            #print(nutrition_attr['nutrientName'])
            nut_name = string_convert_to_search(nutrition_attr['nutrientName'],
                                                flag_sort=False)
            # Find Name
            nut_name_dict = nut_name
            if nut_name not in main_stored_nutrition_names:
                if nut_name in additional_names_to_nurtition_dict.keys():
                    nut_name_dict = additional_names_to_nurtition_dict[nut_name]
                else:
                    print(nut_name)
                    raise Exception('nut_name not found')

            # Covert Units to default (Gram, KCAL)
            value_defunits = convert_units_to_default_usda(nutrition_attr['value'],nutrition_attr['unitName'])

            nutrition_attrs_USDA[nut_name_dict] = value_defunits

        nutrition_attrs_USDA['itemName'] = food.get('description')
        nutrition_attrs_USDA_list.append(nutrition_attrs_USDA)
    return nutrition_attrs_USDA_list

