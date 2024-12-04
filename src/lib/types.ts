// types.svelte 
export interface Book {
     id: number;
     title: string;
     author: string;
     isbn: string;
   }
   
   export interface BorrowedBook {
     bookId: number;
     borrower: string;
     dueDate: Date;
   }