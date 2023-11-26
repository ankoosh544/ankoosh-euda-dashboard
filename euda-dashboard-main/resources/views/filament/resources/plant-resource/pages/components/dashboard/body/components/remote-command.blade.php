<div class="w-full rounded-xl bg-white p-4 shadow dark:border-gray-600 dark:bg-gray-700">
    <h1 class="flex items-center rtl:space-x-reverse text-sm font-medium text-gray-500 dark:text-gray-200 mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="w-5 h-5 mr-1">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
        </svg>
        Remote Commands
    </h1>
  

    <form id="commandForm" action="{{ url('/command') }}" method="POST">
    @csrf
        <label for="floorNumber" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Enter Floor:</label>
        <!-- <input type="text" id="floorNumber" name="floorNumber" class="w-full px-3 py-2 mt-1 text-gray-700 dark:text-gray-300 border rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none" placeholder="" required> -->
        <input type="text" id="floorNumber" name="floorNumber" class="w-full px-3 py-2 mt-1 text-gray-700 dark:text-gray-300 border rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none" placeholder="" required max="[maximum_floor_value]">
        <input type="hidden" name="plantId" value="{{ $data->plantId }}">




        <button type="submit" class="mt-3 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-200">
            Submit
        </button>
    </form>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get a reference to the floorNumber input field
            var floorNumberInput = document.getElementById('floorNumber');
            
            // Get the maximum value from the 'max' attribute
            var maxFloorValue = parseInt(floorNumberInput.getAttribute('max'));
            
            // Add an input event listener to the input field
            floorNumberInput.addEventListener('input', function() {
                var enteredValue = parseInt(floorNumberInput.value);
                
                // Check if the entered value is greater than the maximum
                if (enteredValue > maxFloorValue) {
                    // If greater, set the input value to the maximum value
                    floorNumberInput.value = maxFloorValue;
                }
            });
        });
    </script>


</div>