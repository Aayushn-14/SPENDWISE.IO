import pandas as pd
import mysql.connector
import sys
import os

#file from system args
if len(sys.argv) < 2:
    print("No Excel file path provided.")
    sys.exit(1)

excel_file_path = sys.argv[1]


if not os.path.isfile(excel_file_path):
    print("Excel file does not exist.")
    sys.exit(1)

# Excel file into a DataFrame
df = pd.read_excel(excel_file_path,skiprows=15)

# Replace 'nan' values with None in the DataFrame
df = df.where(pd.notna(df), None)


# Define the expected column names (case-insensitive)
expected_columns = ['Date', 'Particulars', 'Withdrawals', 'Deposits']

# Check for the expected columns (case-insensitive)
actual_columns = [col.lower() for col in df.columns]
missing_columns = [col for col in expected_columns if col.lower() not in actual_columns]

if missing_columns:
    missing_columns_str = ', '.join(missing_columns)
    print(f"Columns '{missing_columns_str}' not found in the DataFrame.")
else:
    try:
        
        db_config = {
            "host": "localhost",
            "user": "root",  
            "password": "",  
            "database": "spendwisedb"  
        }

        conn = mysql.connector.connect(**db_config)

        
        if conn.is_connected():
            cursor = conn.cursor()

            
            table_name = "data"

            delete_sql = f"DELETE FROM {table_name}"
            cursor.execute(delete_sql) 
                        
            for index, row in df.iterrows():
                
                date = str(row['Date'])
                particulars = str(row['Particulars'])

                
                try:
                    withdrawals = float(row['Withdrawals'])
                except (ValueError, TypeError):
                    withdrawals = 0 #default value  

                try:
                    deposits = float(row['Deposits'])
                except (ValueError, TypeError):
                    deposits = 0  #default value

                # Skip rows where both 'Deposits' and 'Withdrawals' are '0' or both are not '0'
                if (withdrawals == 0 and deposits == 0) or (withdrawals != 0 and deposits != 0):
                    continue

                #  insert data into the table
                sql = f"INSERT INTO {table_name} (Date, Particulars, Withdrawals, Deposits) VALUES (%s, %s, %s, %s)"

                # Execute the SQL query
                cursor.execute(sql, (date, particulars, withdrawals, deposits))

            
            conn.commit()
            cursor.close()
            conn.close()
            print(f"Data inserted into the '{table_name}' table of 'spendwisedb' database successfully")

        else:
            print("Database connection failed")
    except mysql.connector.Error as err:
        print("MySQL Error:", err)
        conn.rollback()  
    except Exception as e:
        import traceback
        print("An error occurred:")
        print(traceback.format_exc())
        print("Exception details:", e)
