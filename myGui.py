## GUI
import HandleConversion
import PySimpleGUI as psg
import parsingEngine


# Input Name of food [TEXTBOX SELECTION]:
# Selected Item
# Table:
#   [Name, Value, Unit]



def GuiFoodData(DBItems):
    itemNamesList = []
    for itemName in DBItems:
        itemNamesList.append(itemName)
    toprow = ['יחידות', 'ערך', 'סימון תזונתי']
    rows = [[]]
    psg.set_options(font=("Arial Bold", 14),text_justification="right")
    #lst = psg.Combo(itemNamesList, font=('Arial Bold', 14), expand_x=True, enable_events=True, readonly=False, key="_COMBOINPUT_")
    lst = psg.Listbox([], expand_x=True, key="listboxW", visible=True)
    tbl1 = psg.Table(values=rows, headings=toprow,
       auto_size_columns=True,
       display_row_numbers=False,
       justification='right',
                     key='-TABLE-',
       selected_row_colors='red on yellow',
       enable_events=True,
       expand_x=True,
       expand_y=True,
     enable_click_events=True)
    layout = [[psg.Text('בחר מוצר')],
              [psg.InputText('',enable_events=True,expand_x=True, key="_COMBOINPUT_")],
              [psg.Button('Submit', visible=False, bind_return_key=True)],[tbl1]]
    # [lst],
    window = psg.Window("צפיה בערכים תזונתיים למוצר", layout, size=(715, 200), resizable=True,element_justification="right",finalize=True)
    window["_COMBOINPUT_"].Widget.configure(justify="right")
    selItem = None

    while True:
       event, values = window.read()
       print("event:", event, "values:", values)
       if event == psg.WIN_CLOSED:
          break
       if event == '_COMBOINPUT_':
           curValue = values['_COMBOINPUT_']
           cntFound = 0
           for itemName in itemNamesList:
               if itemName.find(curValue)>=0:
                   selItem = itemName
                   cntFound = cntFound+1
           if cntFound > 1: # No one selection
               selItem = None
           else:
               print(selItem)
       if event == "Submit":
           listNutForTable = []
           if selItem is not None:
               selItemNutritionValues = DBItems[selItem][parsingEngine.__nutritionValues__]
               for nutName in selItemNutritionValues:
                   nutElem = selItemNutritionValues[nutName]
                   # TODO: change 'nutValue' and 'nutUnits' to constants
                   # TODO: Add selection box for multiple items
                   if (nutName!='כפיות סוככפיות סוכר'):
                      convertedValue = HandleConversion.convertUnitFromStandard([nutElem['nutValue'], nutElem['nutUnits']],
                                                                                HandleConversion.__dictNutNameToUnitsForDisplay__[nutName])
                   #convertedNutName = HandleConversion.convertNutName(nutName)

                   # Trim to 3 last digits
                   convertedValue[0] = round(convertedValue[0]*1000)/1000

                   listNutForTable.append([
                       convertedValue[1], # Units
                       convertedValue[0], # Value
                       nutName
                       ])
               tbl1.update(listNutForTable)
       if '+CLICKED+' in event:
          psg.popup("You clicked row:{} Column: {}".format(event[2][0], event[2][1]))
    window.close()



def showTable(toprow,rows):
    #toprow = ['S.No.', 'Name', 'Age', 'Marks']
    #rows = [[1, 'Rajeev', 23, 78],
    #        [2, 'Rajani', 21, 66],
    #        [3, 'Rahul', 22, 60],
    #        [4, 'Robin', 20, 75]]
    psg.set_options(font=("Arial Bold", 14))
    tbl1 = psg.Table(values=rows, headings=toprow,
       auto_size_columns=True,
       display_row_numbers=False,
       justification='center', key='-TABLE-',
       selected_row_colors='red on yellow',
       enable_events=True,
       expand_x=True,
       expand_y=True,
     enable_click_events=True)
    layout = [[tbl1]]
    window = psg.Window("Table Demo", layout, size=(715, 200), resizable=True)
    while True:
       event, values = window.read()
       print("event:", event, "values:", values)
       if event == psg.WIN_CLOSED:
          break
       if '+CLICKED+' in event:
          psg.popup("You clicked row:{} Column: {}".format(event[2][0], event[2][1]))
    window.close()

# TODO: Remove
def guiInteractive():
    toprow = ['S.No.', 'Name', 'Age', 'Marks']
    rows = [[1, 'Rajeev', 23, 78],
             [2, 'Rajani', 21, 66],
             [3, 'Rahul', 22, 60],
             [4, 'Robin', 20, 75]]
    psg.set_options(font=("Arial Bold", 14))
    tbl1 = psg.Table(values=rows, headings=toprow,
       auto_size_columns=True,
       display_row_numbers=False,
       justification='center', key='-TABLE-',
       selected_row_colors='red on yellow',
       enable_events=True,
       expand_x=True,
       expand_y=True,
     enable_click_events=True)
    layout = [[psg.Text('Some text on Row 1')],[tbl1],[psg.Text('Enter something on Row 2'),psg.InputText('',enable_events=False, key='-INPUT-')],
              [psg.Button('Submit', visible=False, bind_return_key=True)]]
    window = psg.Window("Table Demo", layout, size=(715, 200), resizable=True)
    while True:
       event, values = window.read()
       print("event:", event, "values:", values)
       #if event == '-INPUT-':
       #    if values['-INPUT-'][-1])
       #    #print(values['-INPUT-'][-1])
       if event == 'Submit':
            print(window['-INPUT-'].get())
       if event == psg.WIN_CLOSED:
          break
       if '+CLICKED+' in event:
          psg.popup("You clicked row:{} Column: {}".format(event[2][0], event[2][1]))
    window.close()
