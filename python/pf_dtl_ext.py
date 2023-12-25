import pdfplumber
import mysql.connector
import sys

# Function to extract data from the PDF (implement as needed)
def extract_data_from_pdf(pdf_file_path):
    data = {}  # Replace with actual data extraction logic
    return data

if len(sys.argv) < 2:
    print("No PDF file path provided.")
    sys.exit(1)

# Get the uploaded PDF file path from the command-line argument
uploaded_file_path = sys.argv[1]

# Open the PDF file using pdfplumber
with pdfplumber.open(uploaded_file_path) as pdf:
    first_page = pdf.pages[0]  # Access the first page
    pdf_text = first_page.extract_text()  # Extract text from the PDF

# Define the keywords to search for
keywords = ['Account Name', 'Account Number', 'Branch', 'IFS Code', 'Email']

# Initialize variables for data fields
account_name_value = None
account_number_value = None
branch_value = None
ifs_code_value = 'NA'
email_value = 'NA'  # Default value

# Split the extracted text into lines
pdf_lines = pdf_text.split('\n')

# Iterate through the lines to extract the data
for line in pdf_lines:
    for keyword in keywords:
        if keyword in line:
            parts = line.split(keyword)
            if len(parts) > 1:
                value = parts[1].strip()
                if keyword == 'Account Name':
                    account_name_value = value
                elif keyword == 'Account Number':
                    account_number_value = value
                elif keyword == 'Branch':
                    branch_value = value
                elif keyword == 'IFS Code':
                    ifs_code_value = value
                elif keyword == 'Email':
                    email_value = value

# Print extracted values for verification
print("Extracted Values:")
print("Account Name:", account_name_value)
print("Account Number:", account_number_value)
print("Branch:", branch_value)
print("IFS code:", ifs_code_value)
print("Email:", email_value)

# Establish connection to MySQL
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",  # Replace with your MySQL password
    database="spendwisedb"
)

# Check if connection is successful
if conn.is_connected():
    cursor = conn.cursor()

    # SQL INSERT query for 'clients' table with multiple columns
    sql = "INSERT INTO clients (`Name`, `Account Number`, `Bank Name`, `IFSC`, `Email`) VALUES (%s, %s, %s, %s, %s)"
    
    # Example values for insertion (replace these with extracted values)
    values_to_insert = (account_name_value, account_number_value, branch_value, ifs_code_value, email_value)

    try:
        # Execute the INSERT query
        cursor.execute(sql, values_to_insert)
        
        # Commit changes to the database
        conn.commit()
        print("Data inserted into the database table successfully")
    except mysql.connector.Error as err:
        print("MySQL Error:", err)
        conn.rollback()  # Rollback changes if an error occurs
    except Exception as e:
        print("An error occurred:")
        print("Exception details:", e)

    # Close cursor and connection
    cursor.close()
    conn.close()
else:
    print("Connection to MySQL failed")
