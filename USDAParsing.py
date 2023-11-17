import requests
import requests_cache
from bs4 import BeautifulSoup
#from myGui import showTable
import pandas as pd


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
    str_no_special = str_lower.replace(',',' ').replace('-',' ').replace('_',' ').replace('(',' ').replace(')',' ')
    arr_words = str_no_special.split(' ')
    arr_words_no_empty = []
    for word in arr_words:
        if (len(word)>0):
            arr_words_no_empty.append(word)
    if (flag_sort):
        arr_words_no_empty.sort(reverse=sort_order_up_ndown)

    return '_'.join(arr_words_no_empty)


def get_nutrition_values(itemName):
    # urlFoods = 'curl https://api.nal.usda.gov/fdc/v1/foods/search?api_key=DEMO_KEY&query=Cheddar%20Cheese'

    myFoodName = "tomato, raw"  # "tomato"
    urlFoodsGet = "https://api.nal.usda.gov/fdc/v1/foods/search?api_key={ApiKey}&query={foodName}&dataType=Survey (FNDDS)".format(
        ApiKey=__myApiKey__, foodName=myFoodName)

    session = requests_cache.CachedSession('cacheUrlName')
    r = session.get(urlFoodsGet)
    if not r.from_cache:
        print(f'{urlFoodsGet} not from cache!')
    # r = requests.get(url=urlFoodsGet) # TODO: Change to cached
    # print(r)

    import database_handler
    import globalContants as gc
    stored_nutrition_names = database_handler.DatabaseHandler().loadAllRows(gc.__table_nutrition_attribute_name__,['nutritionName','additionalNames'])
    main_stored_nutrition_names = [stored_nutrition_names[ii][0] for ii in range(len(stored_nutrition_names))]
    additional_nutrition_names = [stored_nutrition_names[ii][1] for ii in range(len(stored_nutrition_names))]
    additional_names_to_nurtition_dict = dict()
    for ii in range(len(additional_nutrition_names)):
        add_name = additional_nutrition_names[ii]
        if len(add_name)>0:
            add_name_arr = add_name.split(';')
            for add_name_single in add_name_arr:
                additional_names_to_nurtition_dict[add_name_single] = main_stored_nutrition_names[ii]
    tmp_data_list = r.json().get('foods')[0].get('foodNutrients')
    for nutrition_attr in tmp_data_list:
        #print(nutrition_attr['nutrientName'])
        nut_name = string_convert_to_search(nutrition_attr['nutrientName'],
                                            flag_sort=False)
        if nut_name not in main_stored_nutrition_names:
            if nut_name not in additional_names_to_nurtition_dict.keys():
                print(nut_name)




    dictNutrientToHebrew = {"Protein": "חלבון",
                            "Total lipid (fat)": "שומנים",
                            "Carbohydrate, by difference": "פחמימות"}
    for ii in range(3):  # len(r.json().get('foods')[0])):
        nJSON = r.json().get('foods')[0].get('foodNutrients')[ii]
        nName = nJSON['nutrientName']
        nHebName = dictNutrientToHebrew[nName]
        nUnitName = nJSON['unitName']
        nValue = nJSON['value']  # TODO: Multiple by unitName
        print("גרם {}={}".format(nValue, nHebName))

# Old
def ParseUrl(url):
    if __flagChachingSite__:
        session = requests_cache.CachedSession('cacheUrlName')
        r = session.get(url)
    else:
        session = requests.session()
        r = session.get(url)

    textToParse = r.text
    soup = BeautifulSoup(textToParse, 'html.parser')
    table = soup.find('table', class_='nv-table')
    nutValueTableRows = None
    for nutData in table.find_all('tr'):
        nutDataEnt = nutData.find_all('td')
        if (len(nutDataEnt) > 1):
            nutName = nutDataEnt[0].text
            # m = re.search(r'\((.*?)\)', nutName)
            nutUnits = nutName[nutName.find("(") + 1:nutName.find(")")]
            if nutName.find("("):
                nutNameWOUnits = nutName[:nutName.find("(")].strip() + (nutName[nutName.find(")") + 1:]).strip()
            else:
                nutNameWOUnits = nutName
            nutValue = nutDataEnt[1].get('data-start')
            if (nutValue is None):
                nutValue = 0
            # print("{} = {}, {}".format(nutNameWOUnits,nutValue,nutNameUnits))
            if nutValueTableRows is None:
                nutValueTableRows = pd.DataFrame({nutNameWOUnits: [nutValue, nutUnits]}, index=['nutValue', 'nutUnits'])
            else:
                nutValueTableRows.insert(0,nutNameWOUnits,[nutValue, nutUnits])
        else:
           for line in nutData.find_all('th'):
                if line.get('id')=='sizeNameTd':
                    if not line.text.find('100 גרם'):
                        print(line)
                        raise Exception('error in parsing, size is not 100 grams!')
    # showTable(['Name', 'Value', 'Units'],nutValueTableRows)
    # urlFoods = 'curl https://api.nal.usda.gov/fdc/v1/foods/search?api_key=DEMO_KEY&query=Cheddar%20Cheese'

    myFoodName = dictHebrewToFNDDS[foodHebName]  # "tomato"
    urlFoodsGet = "https://api.nal.usda.gov/fdc/v1/foods/search?api_key={ApiKey}&query={foodName}&dataType=Survey (FNDDS)".format(
        ApiKey=myApiKey, foodName=myFoodName)
    r = requests.get(url=urlFoodsGet) # TODO: Change to cached
    # print(r)
    dictNutrientToHebrew = {"Protein": "חלבון",
                            "Total lipid (fat)": "שומנים",
                            "Carbohydrate, by difference": "פחמימות"}
    for ii in range(3):  # len(r.json().get('foods')[0])):
        nJSON = r.json().get('foods')[0].get('foodNutrients')[ii]
        nName = nJSON['nutrientName']
        nHebName = dictNutrientToHebrew[nName]
        nUnitName = nJSON['unitName']
        nValue = nJSON['value']  # TODO: Multiple by unitName
        print("גרם {}={}".format(nValue, nHebName))

    return nutValueTableRows

