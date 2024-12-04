<script lang="ts">
     import type { Book, BorrowedBook } from '../types';
   
     export let borrowedBooks: BorrowedBook[];
     export let books: Book[];
     export let returnBook: (id: number) => void;
   
     function getBookTitle(bookId: number): string {
       const book = books.find(b => b.id === bookId);
       return book ? book.title : 'Unknown Book';
     }
   
     function isOverdue(dueDate: Date): boolean {
       return new Date() > new Date(dueDate);
     }
   </script>
   
   <ul class="space-y-4">
     {#each borrowedBooks as borrowed (borrowed.bookId)}
       <li class="bg-white p-4 rounded shadow">
         <h3 class="text-xl font-semibold">{getBookTitle(borrowed.bookId)}</h3>
         <p>Borrower: {borrowed.borrower}</p>
         <p class:text-red-500={isOverdue(borrowed.dueDate)}>
           Due Date: {borrowed.dueDate.toLocaleDateString()}
         </p>
         <button on:click={() => returnBook(borrowed.bookId)} class="bg-blue-500 text-white px-2 py-1 rounded mt-2">
           Return Book
         </button>
       </li>
     {/each}
   </ul>