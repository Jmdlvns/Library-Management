<script lang="ts">
     import type { Book, BorrowedBook } from '../types';
   
     export let borrowedBooks: BorrowedBook[];
     export let books: Book[];
   
     const FINE_PER_DAY = 5; // PHP 5 per day 
     
     function getBookTitle(bookId: number): string {
       const book = books.find(b => b.id === bookId);
       return book ? book.title : 'Unknown Book';
     }
   
     function calculateFine(dueDate: Date): number {
       const today = new Date();
       const due = new Date(dueDate);
       const diffTime = Math.max(today.getTime() - due.getTime(), 0);
       const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
       return diffDays * FINE_PER_DAY;
     }
   
     $: overdueBorrows = borrowedBooks.filter(borrowed => new Date() > new Date(borrowed.dueDate));
   </script>
   
   <ul class="space-y-4">
     {#each overdueBorrows as borrowed (borrowed.bookId)}
       <li class="bg-white p-4 rounded shadow">
         <h3 class="text-xl font-semibold">{getBookTitle(borrowed.bookId)}</h3>
         <p>Borrower: {borrowed.borrower}</p>
         <p>Due Date: {borrowed.dueDate.toLocaleDateString()}</p>
         <p class="text-red-500">Fine: ₱{calculateFine(borrowed.dueDate).toFixed(2)}</p>
       </li>
     {/each}
   </ul>
   