<!DOCTYPE html>
<html>
<head>
    <script src="https://d3js.org/d3.v5.min.js"></script>
</head>
<body>
    <svg id="myChart" width="100%" height="400"></svg>

    <?php
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "spendwisedb";

    // Create a connection to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to retrieve data from the database
    $query = "SELECT Date, Withdrawals, Deposits FROM Data";
    $result = $conn->query($query);

    $dataPoints = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $date = $row["Date"];
            $withdrawals = $row["Withdrawals"];
            $deposits = $row["Deposits"];
            $dataPoints[] = array("date" => $date, "withdrawals" => $withdrawals, "deposits" => $deposits);
        }
    }

    // Close the database connection
    $conn->close();
    ?>

    <script>
        // JavaScript code to create the line chart using D3.js
        const dataPoints = <?php echo json_encode($dataPoints); ?>;
        
        // Function to create the line chart
        function createLineChart(data) {
            const svg = d3.select("#myChart");
            const margin = { top: 20, right: 30, bottom: 40, left: 40 };
            const width = svg.attr("width") - margin.left - margin.right;
            const height = svg.attr("height") - margin.top - margin.bottom;
            
            const xScale = d3.scaleTime()
                .domain(d3.extent(data, d => d.date))
                .range([margin.left, width]);

            const yScale = d3.scaleLinear()
                .domain([0, d3.max(data, d => Math.max(d.withdrawals, d.deposits))])
                .nice()
                .range([height, margin.top]);
            
            const withdrawalsLine = d3.line()
                .x(d => xScale(d.date))
                .y(d => yScale(d.withdrawals));

            const depositsLine = d3.line()
                .x(d => xScale(d.date))
                .y(d => yScale(d.deposits));
            
            const g = svg.append("g")
                .attr("transform", `translate(${margin.left}, ${margin.top})`);
            
            g.append("path")
                .data([data])
                .attr("class", "line")
                .attr("d", withdrawalsLine)
                .style("stroke", "#b91d47");
            
            g.append("path")
                .data([data])
                .attr("class", "line")
                .attr("d", depositsLine)
                .style("stroke", "#00aba9");
            
            g.append("g")
                .attr("class", "x axis") // Added the class "x axis"
                .attr("transform", `translate(0, ${height})`)
                .call(d3.axisBottom(xScale).tickFormat(d3.timeFormat("%Y-%m-%d")));
            
            g.append("g")
                .attr("class", "y axis")
                .call(d3.axisLeft(yScale));
            
            g.select(".x.axis") // Select the "x axis" class
                .append("text")
                .attr("class", "x label")
                .attr("text-anchor", "end")
                .attr("x", width)
                .attr("y", 35)
                .text("Date");
            
            g.select(".x.axis") // Select the "x axis" class
                .append("text")
                .attr("class", "x label")
                .attr("text-anchor", "end")
                .attr("x", width)
                .attr("y", 55)
                .text("More Date");
            
            g.append("text")
                .attr("class", "y label")
                .attr("text-anchor", "end")
                .attr("y", -40)
                .attr("x", -40)
                .attr("dy", ".75em")
                .attr("transform", "rotate(-90)")
                .text("Amount");
        }

        // Call the function to create the chart
        createLineChart(dataPoints);
    </script>
</body>
</html>
