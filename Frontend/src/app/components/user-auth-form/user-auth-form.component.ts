import { Component, ElementRef, Input, ViewChild } from "@angular/core";
import { AuthFormEnum } from "../../../enums/auth-form.enum";
import { ButtonComponent } from "../button/button.component";
import { IconComponent } from "../icon/icon.component";
import { Router } from "@angular/router";

@Component({
  selector: "app-user-auth-form",
  imports: [ButtonComponent, IconComponent],
  templateUrl: "./user-auth-form.component.html",
  styleUrl: "./user-auth-form.component.css",
})
export class UserAuthFormComponent {
  @Input()
  public type: AuthFormEnum = AuthFormEnum.LOGIN;

  @ViewChild("userNameInput")
  private userNameInput?: ElementRef;

  @ViewChild("emailInput")
  private emailInput?: ElementRef;

  @ViewChild("passwordInput")
  private passwordInput?: ElementRef;

  protected readonly iconSize: number = 64;
  protected readonly AuthFormEnum = AuthFormEnum;

  constructor(private readonly router: Router) {}

  protected handleSubmit(event: Event) {
    const submitEvent = event as SubmitEvent;
    submitEvent.preventDefault();

    const userName = (this.userNameInput?.nativeElement as HTMLInputElement).value;
    const email = (this.emailInput?.nativeElement as HTMLInputElement).value;
    const password = (this.passwordInput?.nativeElement as HTMLInputElement).value;
  }

  protected async goToOtherAuthForm() {
    const route = this.type == AuthFormEnum.LOGIN ? "/signup" : "/login";
    await this.router.navigateByUrl(route);
  }

  private handleAuth() {}
}
