import { Component } from "@angular/core";
import { AuthController } from "../../../controllers/auth.controller";

@Component({
  selector: "app-account",
  imports: [],
  templateUrl: "./account.component.html",
  styleUrl: "./account.component.css",
})
export class AccountComponent {
  constructor(protected readonly authController: AuthController) {}
}
