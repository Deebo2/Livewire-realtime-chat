<div class=" fixed  h-full flex bg-white dark:bg-gray-800  dark:border-gray-700 border lg:shadow-sm overflow-hidden inset-0 lg:top-16 lg:inset-x-2 m-auto lg:h-[90%]   rounded-lg" >
    <div class=" hidden lg:flex   relative dark:border-gray-600 w-full h-full md:w-[320px] xl:w-[400px] border-0 shrink-0 overflow-y-auto  ">
           <livewire:chat.chat-list :selectedConversation="$selectedConversation" :query="$query">
    </div>

    <main class="relative grid w-full h-full overflow-y-auto border-l dark:border-gray-700"  style="contain:content">
       
           <livewire:chat.chat-box :selectedConversation="$selectedConversation">
        
    </main>
</div>
