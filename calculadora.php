<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calculadora Mini - Campamento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #692828; }
        .bg-card-yellow { background-color: #f8eb87; }
        .bg-input-cream { background-color: #fffbe6; }
        
        .calc-btn {
            background-color: #fffbe6;
            transition: all 0.1s;
            user-select: none;
        }
        .calc-btn:focus { outline: none !important; ring: 0 !important; }
        .calc-btn:active { transform: scale(0.95); background-color: #f1e695; }
        
        .op-btn { background-color: #8f2f2f; color: white; }
        .op-btn:active { background-color: #6e1f1f; }
    </style>
</head>
<body class="min-h-screen p-4 flex justify-center items-center relative">

    <div class="absolute top-6 left-6">
        <a href="home.php" class="bg-[#8f2f2f] text-white px-6 py-3 rounded-full hover:bg-[#6e1f1f] cursor-pointer font-extrabold transition duration-150 shadow-xl flex items-center">
            <i class="fas fa-chevron-left mr-2"></i> INICIO
        </a>
    </div>

    <div class="bg-card-yellow w-full max-w-xs p-6 rounded-[35px] shadow-2xl border-4 border-[#fffbe6]/30 mt-12">
        
        <div class="flex justify-between items-center mb-4 px-2">
            <h2 class="text-xs font-black text-[#692828] uppercase tracking-widest italic">Calculadora</h2>
            <i class="fas fa-calculator text-[#692828]/20"></i>
        </div>

        <div class="bg-input-cream rounded-2xl p-4 mb-4 shadow-inner text-right border border-[#692828]/10">
            <div id="prev-operation" class="text-[#692828]/40 text-[10px] h-4 font-semibold"></div>
            <div id="display" class="text-3xl font-bold text-[#692828] truncate">0</div>
        </div>

        <div class="grid grid-cols-4 gap-2">
            <button onclick="clearDisplay()" class="calc-btn focus:outline-none focus:ring-0 p-3 rounded-xl font-bold text-[10px] text-red-700 border border-red-200">AC</button>
            <button onclick="appendOp('%')" class="calc-btn focus:outline-none focus:ring-0 p-3 rounded-xl font-bold text-[#692828] text-xs border border-[#692828]/10">%</button>
            <button onclick="deleteLast()" class="calc-btn focus:outline-none focus:ring-0 p-3 rounded-xl font-bold text-[#692828] text-xs border border-[#692828]/10"><i class="fas fa-backspace text-xs"></i></button>
            <button onclick="appendOp('/')" class="calc-btn focus:outline-none focus:ring-0 op-btn p-3 rounded-xl font-bold text-xs">/</button>
            
            <button onclick="appendNum('7')" class="calc-btn focus:outline-none focus:ring-0 p-3 rounded-xl font-bold text-[#692828] border border-[#692828]/10">7</button>
            <button onclick="appendNum('8')" class="calc-btn focus:outline-none focus:ring-0 p-3 rounded-xl font-bold text-[#692828] border border-[#692828]/10">8</button>
            <button onclick="appendNum('9')" class="calc-btn focus:outline-none focus:ring-0 p-3 rounded-xl font-bold text-[#692828] border border-[#692828]/10">9</button>
            <button onclick="appendOp('*')" class="calc-btn focus:outline-none focus:ring-0 op-btn p-3 rounded-xl font-bold text-xs">×</button>

            <button onclick="appendNum('4')" class="calc-btn focus:outline-none focus:ring-0 p-3 rounded-xl font-bold text-[#692828] border border-[#692828]/10">4</button>
            <button onclick="appendNum('5')" class="calc-btn focus:outline-none focus:ring-0 p-3 rounded-xl font-bold text-[#692828] border border-[#692828]/10">5</button>
            <button onclick="appendNum('6')" class="calc-btn focus:outline-none focus:ring-0 p-3 rounded-xl font-bold text-[#692828] border border-[#692828]/10">6</button>
            <button onclick="appendOp('-')" class="calc-btn focus:outline-none focus:ring-0 op-btn p-3 rounded-xl font-bold text-xs">-</button>

            <button onclick="appendNum('1')" class="calc-btn focus:outline-none focus:ring-0 p-3 rounded-xl font-bold text-[#692828] border border-[#692828]/10">1</button>
            <button onclick="appendNum('2')" class="calc-btn focus:outline-none focus:ring-0 p-3 rounded-xl font-bold text-[#692828] border border-[#692828]/10">2</button>
            <button onclick="appendNum('3')" class="calc-btn focus:outline-none focus:ring-0 p-3 rounded-xl font-bold text-[#692828] border border-[#692828]/10">3</button>
            <button onclick="appendOp('+')" class="calc-btn focus:outline-none focus:ring-0 op-btn p-3 rounded-xl font-bold text-xs">+</button>

            <button onclick="appendNum('0')" class="calc-btn focus:outline-none focus:ring-0 col-span-2 p-3 rounded-xl font-bold text-[#692828] border border-[#692828]/10">0</button>
            <button onclick="appendNum('1')" class="calc-btn focus:outline-none focus:ring-0 p-3 rounded-xl font-bold text-[#692828] border border-[#692828]/10">.</button>
            <button onclick="calculate()" class="calc-btn focus:outline-none focus:ring-0 bg-[#8f2f2f] text-white p-3 rounded-xl font-bold shadow-lg hover:bg-[#6e1f1f]">=</button>
        </div>
    </div>

    <script>
        let display = document.getElementById('display');
        let prevOp = document.getElementById('prev-operation');
        let currentInput = '0';

        function updateDisplay() {
            display.innerText = currentInput;
        }

        function appendNum(num) {
            if (currentInput === '0' && num !== '.') {
                currentInput = num;
            } else {
                currentInput += num;
            }
            updateDisplay();
        }

        function appendOp(op) {
            const lastChar = currentInput.slice(-1);
            if (['+', '-', '*', '/', '%'].includes(lastChar)) {
                currentInput = currentInput.slice(0, -1) + op;
            } else {
                currentInput += op;
            }
            updateDisplay();
        }

        function clearDisplay() {
            currentInput = '0';
            prevOp.innerText = '';
            updateDisplay();
        }

        function deleteLast() {
            if (currentInput.length > 1) {
                currentInput = currentInput.slice(0, -1);
            } else {
                currentInput = '0';
            }
            updateDisplay();
        }

        function calculate() {
            try {
                let expression = currentInput.replace(/(\d+)%/g, '($1/100)');
                prevOp.innerText = currentInput + ' =';
                currentInput = String(eval(expression));
                updateDisplay();
            } catch (e) {
                display.innerText = 'Error';
                setTimeout(clearDisplay, 1000);
            }
        }
    </script>
</body>
</html>